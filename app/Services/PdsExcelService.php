<?php

namespace App\Services;

use App\Models\PersonalDataSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf as SpreadsheetPdfWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Finder\Finder;

/**
 * Generate PDS (Personal Data Sheet) Excel file from the official template,
 * filling in personnel data and optional photo.
 */
class PdsExcelService
{
    public function __construct(
        protected PdsPhotoService $photoService
    ) {}

    /**
     * Build the filled Spreadsheet from the PDS template and data (no file write).
     */
    public function buildSpreadsheet(PersonalDataSheet $pds): Spreadsheet
    {
        $templatePath = $this->resolveTemplatePath();
        if ($templatePath === null || ! is_readable($templatePath)) {
            throw new \RuntimeException('PDS Excel template not found. Place an .xlsx template in storage/app/pds-templates/ or set config pds-excel.template_path.');
        }

        $spreadsheet = IOFactory::load($templatePath);

        // Remove all named ranges to prevent Excel corruption
        $namedRanges = $spreadsheet->getNamedRanges();
        foreach ($namedRanges as $namedRange) {
            $spreadsheet->removeNamedRange($namedRange->getName());
        }

        // Remove all defined names to prevent corruption
        $definedNames = $spreadsheet->getDefinedNames();
        foreach ($definedNames as $definedName) {
            $spreadsheet->removeDefinedName($definedName->getName());
        }

        // Save and reload to clean any corrupted XML
        $tempPath = tempnam(sys_get_temp_dir(), 'clean_template');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);
        $spreadsheet = IOFactory::load($tempPath);
        unlink($tempPath);

        $cells = config('pds-excel.cells', []);
        $pds->loadMissing(['civilServiceEligibilities', 'workExperiences', 'voluntaryWorks', 'learningDevelopments']);

        foreach ($cells as $field => $config) {
            if ($config === null) {
                continue;
            }
            $value = $this->formatValue($pds->getAttribute($field), $field);
            if ($value === null || $value === '') {
                continue;
            }
            $sheetIndex = $config['sheet'];
            $cell = $config['cell'];
            $sheet = $spreadsheet->getSheet($sheetIndex);
            $sheet->setCellValue($cell, $value);
        }

        $this->fillChildren($spreadsheet, $pds);
        $this->fillRepeatingSection($spreadsheet, $pds, 'civil_service', $pds->civilServiceEligibilities, [
            'date_exam_conferment' => 'date',
            'license_valid_until' => 'date',
        ]);
        $this->fillRepeatingSection($spreadsheet, $pds, 'work_experience', $pds->workExperiences, [
            'from_date' => 'date',
            'to_date' => 'date',
        ]);
        $this->fillRepeatingSection($spreadsheet, $pds, 'voluntary_work', $pds->voluntaryWorks ?? collect(), [
            'inclusive_dates_from' => 'date',
            'inclusive_dates_to' => 'date',
        ]);
        $this->fillRepeatingSection($spreadsheet, $pds, 'learning_development', $pds->learningDevelopments ?? collect(), [
            'inclusive_dates_from' => 'date',
            'inclusive_dates_to' => 'date',
        ]);
        $this->fillOtherInformation($spreadsheet, $pds);
        $this->fillPage4YesNo($spreadsheet, $pds);
        $this->fillPage4Details($spreadsheet, $pds);
        $this->fillPage4References($spreadsheet, $pds);
        $this->fillPage4GovtId($spreadsheet, $pds);

        // Photo injection temporarily disabled for testing
        // $this->addPhotoIfExists($spreadsheet, $pds);

        return $spreadsheet;
    }

    /**
     * Generate Excel file for the given PDS. Returns path to the generated file (temp).
     */
    public function generate(PersonalDataSheet $pds): string
    {
        $spreadsheet = $this->buildSpreadsheet($pds);

        $outPath = storage_path('app/temp-pds-' . $pds->id . '-' . uniqid() . '.xlsx');
        $dir = dirname($outPath);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($outPath);

        // Clean named ranges from the generated file by directly manipulating the XML
        $this->cleanXlsxNamedRanges($outPath);

        return $outPath;
    }

    /**
     * Clean named ranges and invalid XML from generated XLSX file by directly manipulating the ZIP contents.
     */
    protected function cleanXlsxNamedRanges(string $filePath): void
    {
        if (! file_exists($filePath)) {
            return;
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return;
        }

        // Clean workbook.xml - remove definedNames section
        $workbookContent = $zip->getFromName('xl/workbook.xml');
        if ($workbookContent !== false) {
            $cleanedContent = preg_replace('/<definedNames>.*?<\/definedNames>/s', '', $workbookContent);
            if ($cleanedContent !== $workbookContent) {
                $zip->deleteName('xl/workbook.xml');
                $zip->addFromString('xl/workbook.xml', $cleanedContent);
            }
        }

        // Clean worksheet files - remove invalid tableParts with count="0"
        for ($i = 1; $i <= 10; $i++) {
            $sheetPath = "xl/worksheets/sheet{$i}.xml";
            $sheetContent = $zip->getFromName($sheetPath);
            if ($sheetContent === false) {
                continue;
            }

            $originalContent = $sheetContent;
            // Remove invalid tableParts with count="0"
            $sheetContent = preg_replace('/<tableParts count="0"\/>/', '', $sheetContent);
            // Remove empty tableParts elements
            $sheetContent = preg_replace('/<tableParts[^>]*>\s*<\/tableParts>/', '', $sheetContent);

            if ($sheetContent !== $originalContent) {
                $zip->deleteName($sheetPath);
                $zip->addFromString($sheetPath, $sheetContent);
            }
        }

        // Clean worksheet relationship files - remove duplicate relationship IDs
        for ($i = 1; $i <= 10; $i++) {
            $relsPath = "xl/worksheets/_rels/sheet{$i}.xml.rels";
            $relsContent = $zip->getFromName($relsPath);
            if ($relsContent === false) {
                continue;
            }

            $originalContent = $relsContent;
            
            // Parse and remove duplicate relationship IDs
            $relsContent = $this->removeDuplicateRelationships($relsContent);
            
            if ($relsContent !== $originalContent) {
                $zip->deleteName($relsPath);
                $zip->addFromString($relsPath, $relsContent);
            }
        }

        $zip->close();
    }

    /**
     * Remove duplicate relationship IDs from relationships XML content.
     */
    protected function removeDuplicateRelationships(string $relsContent): string
    {
        // Parse the XML
        $xml = new \SimpleXMLElement($relsContent);
        $namespaces = $xml->getNamespaces(true);
        $xml->registerXPathNamespace('r', $namespaces[''] ?? 'http://schemas.openxmlformats.org/package/2006/relationships');
        
        // Track seen IDs
        $seenIds = [];
        $duplicates = [];
        
        foreach ($xml->Relationship as $rel) {
            $id = (string) $rel['Id'];
            if (isset($seenIds[$id])) {
                $duplicates[] = $id;
            } else {
                $seenIds[$id] = true;
            }
        }
        
        // If no duplicates, return original
        if (empty($duplicates)) {
            return $relsContent;
        }
        
        // Remove duplicates by rebuilding the XML
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $relationships = $dom->getElementsByTagName('Relationship');
        $seenIds = [];
        $toRemove = [];
        
        foreach ($relationships as $rel) {
            $id = $rel->getAttribute('Id');
            if (isset($seenIds[$id])) {
                $toRemove[] = $rel;
            } else {
                $seenIds[$id] = true;
            }
        }
        
        foreach ($toRemove as $rel) {
            $rel->parentNode->removeChild($rel);
        }
        
        return $dom->saveXML();
    }

    /**
     * Generate PDF from the PDS Excel template (same layout as Excel). Returns path to the generated file (temp).
     * Execution time limit is raised because Dompdf can exceed default when processing Excel-derived CSS.
     */
    public function generatePdf(PersonalDataSheet $pds): string
    {
        set_time_limit(300);
        $spreadsheet = $this->buildSpreadsheet($pds);
        $outPath = storage_path('app/temp-pds-' . $pds->id . '-' . uniqid() . '.pdf');
        $dir = dirname($outPath);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $writer = new SpreadsheetPdfWriter($spreadsheet);
        $writer->writeAllSheets();
        $writer->save($outPath);

        return $outPath;
    }

    /**
     * Diagnose why the PDS photo may not appear in the Excel export (for pds:debug-photo command).
     */
    public function diagnosePhotoExport(PersonalDataSheet $pds): array
    {
        $photoConfig = config('pds-excel.photo');
        $targetSheet = $photoConfig['sheet'] ?? 3;
        $targetCell = $photoConfig['cell'] ?? 'K56';

        $resolvedPath = $this->photoService->absolutePath($pds->photo_path);
        $templatePath = $this->resolveTemplatePath();
        $templateExists = $templatePath !== null && is_readable($templatePath);
        $sheetCount = 0;
        if ($templateExists) {
            try {
                $spreadsheet = IOFactory::load($templatePath);
                $sheetCount = $spreadsheet->getSheetCount();
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $wouldAdd = ! empty($pds->photo_path)
            && $resolvedPath !== null
            && is_readable($resolvedPath)
            && $templateExists
            && $sheetCount > 0
            && $targetSheet < $sheetCount;

        $candidates = [];
        if ($pds->photo_path) {
            $candidates[] = [
                'label' => 'storage/app/public/' . $pds->photo_path,
                'path' => storage_path('app/public/' . $pds->photo_path),
                'exists' => file_exists(storage_path('app/public/' . $pds->photo_path)),
                'readable' => is_readable(storage_path('app/public/' . $pds->photo_path)),
            ];
        }
        if ($resolvedPath) {
            $candidates[] = [
                'label' => 'PdsPhotoService::absolutePath()',
                'path' => $resolvedPath,
                'exists' => file_exists($resolvedPath),
                'readable' => is_readable($resolvedPath),
            ];
        }

        return [
            'pds_id' => $pds->id,
            'photo_path' => $pds->photo_path,
            'normalized_path' => $pds->photo_path ? ltrim($pds->photo_path, '/\\') : null,
            'resolved_absolute_path' => $resolvedPath,
            'resolved_exists' => $resolvedPath ? file_exists($resolvedPath) : false,
            'resolved_readable' => $resolvedPath ? is_readable($resolvedPath) : false,
            'candidates_checked' => $candidates,
            'template_path' => $templatePath,
            'template_exists' => $templateExists,
            'sheet_count' => $sheetCount,
            'photo_config_sheet' => $targetSheet,
            'target_sheet_index' => $targetSheet,
            'target_cell' => $targetCell,
            'would_add_photo' => $wouldAdd,
        ];
    }

    protected function resolveTemplatePath(): ?string
    {
        $configured = config('pds-excel.template_path');
        if ($configured !== null && $configured !== '') {
            $path = is_string($configured) ? $configured : $configured;
            return file_exists($path) ? $path : null;
        }
        $dir = storage_path('app/pds-templates');
        if (! is_dir($dir)) {
            return null;
        }
        $finder = (new Finder())->in($dir)->files()->name('*.xlsx');
        $files = iterator_to_array($finder, false);
        return count($files) > 0 ? $files[0]->getPathname() : null;
    }

    protected function formatValue(mixed $value, string $field): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof \Carbon\Carbon || $value instanceof \DateTimeInterface) {
            return $value->format('m/d/Y');
        }
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        $str = (string) $value;
        if ($field === 'sex' || $field === 'civil_status') {
            return ucfirst(strtolower($str));
        }
        return $str;
    }

    protected function fillChildren(Spreadsheet $spreadsheet, PersonalDataSheet $pds): void
    {
        $config = config('pds-excel.children');
        if (! $config) {
            return;
        }
        $sheet = $spreadsheet->getSheet($config['sheet']);
        $namesArray = [];
        $dobArray = [];
        $childrenData = $pds->children_data;
        if (is_array($childrenData) && ! empty($childrenData)) {
            foreach ($childrenData as $row) {
                $namesArray[] = trim($row['name'] ?? '');
                $dob = $row['dob'] ?? null;
                if ($dob instanceof \DateTimeInterface) {
                    $dob = $dob->format('m/d/Y');
                } elseif (is_string($dob) && $dob !== '') {
                    try {
                        $dob = \Carbon\Carbon::parse($dob)->format('m/d/Y');
                    } catch (\Throwable $e) {
                        // keep as-is if not parseable
                    }
                }
                $dobArray[] = $dob;
            }
        } else {
            $names = $pds->children_names;
            $namesArray = is_string($names) ? array_filter(array_map('trim', explode("\n", $names))) : [];
            $childrenDob = $pds->children_dob ?? null;
            if (is_string($childrenDob)) {
                $dobArray = array_filter(array_map('trim', explode("\n", $childrenDob)));
            }
        }
        $nameCol = $config['name_column'];
        $dobCol = $config['dob_column'];
        $startRow = $config['start_row'];
        $maxRows = $config['max_rows'] ?? 12;

        for ($i = 0; $i < $maxRows; $i++) {
            $row = $startRow + $i;
            $name = $namesArray[$i] ?? '';
            $dob = $dobArray[$i] ?? null;
            if ($dob instanceof \DateTimeInterface) {
                $dob = $dob->format('m/d/Y');
            }
            $sheet->setCellValue($nameCol . $row, $name);
            $sheet->setCellValue($dobCol . $row, $dob);
        }
    }

    /**
     * Section VIII – Other Information: each field is split by newlines and written to
     * separate cells (e.g. A42:A48, C42:C48, I42:I48), one line per row.
     */
    protected function fillOtherInformation(Spreadsheet $spreadsheet, PersonalDataSheet $pds): void
    {
        $config = config('pds-excel.other_information');
        if (! $config || empty($config['columns'])) {
            return;
        }
        $sheet = $spreadsheet->getSheet($config['sheet']);
        $startRow = $config['start_row'];
        $maxRows = $config['max_rows'] ?? 7;
        $columns = $config['columns'];

        $toLines = function (?string $value): array {
            if ($value === null || $value === '') {
                return [];
            }
            return array_values(array_filter(array_map('trim', explode("\n", $value))));
        };

        $linesByField = [];
        foreach ($columns as $field => $colLetter) {
            $linesByField[$field] = $toLines($pds->getAttribute($field));
        }

        for ($i = 0; $i < $maxRows; $i++) {
            $row = $startRow + $i;
            foreach ($columns as $field => $colLetter) {
                $line = $linesByField[$field][$i] ?? '';
                $sheet->setCellValue($colLetter . $row, $line);
            }
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model>  $items
     * @param  array<string, string>  $dateFields  field => 'date'
     */
    protected function fillRepeatingSection(Spreadsheet $spreadsheet, PersonalDataSheet $pds, string $sectionKey, $items, array $dateFields = []): void
    {
        $config = config("pds-excel.{$sectionKey}");
        if (! $config || empty($config['columns'])) {
            return;
        }
        $sheet = $spreadsheet->getSheet($config['sheet']);
        $startRow = $config['start_row'];
        $maxRows = $config['max_rows'] ?? 20;
        $columns = $config['columns'];
        $rows = $items instanceof \Illuminate\Support\Collection ? $items : collect($items);

        $rowIndex = 0;
        foreach ($rows as $item) {
            if ($rowIndex >= $maxRows) {
                break;
            }
            $row = $startRow + $rowIndex;
            foreach ($columns as $field => $col) {
                $value = $item->getAttribute($field);
                if (isset($dateFields[$field]) && $value instanceof \DateTimeInterface) {
                    $value = $value->format('m/d/Y');
                }
                $sheet->setCellValue($col . $row, $value ?? '');
            }
            $rowIndex++;
        }
    }

    /**
     * Page 4 (Q34–40): For each Y/N question, put "✓ YES" or "✓ NO" in the corresponding cell.
     * If no answer (null/empty), leave both cells blank.
     */
    protected function fillPage4YesNo(Spreadsheet $spreadsheet, PersonalDataSheet $pds): void
    {
        $config = config('pds-excel.page4_yn');
        if (! $config || empty($config['questions'])) {
            return;
        }
        $sheetIndex = $config['sheet'] ?? 3;
        if ($spreadsheet->getSheetCount() <= $sheetIndex) {
            return;
        }
        $sheet = $spreadsheet->getSheet($sheetIndex);
        $checkChar = $config['check_char'] ?? '✓';

        foreach ($config['questions'] as $field => $cells) {
            $value = $pds->getAttribute($field);
            if ($value === null || $value === '') {
                continue;
            }
            $value = strtoupper((string) $value);
            if ($value === 'Y') {
                $sheet->setCellValue($cells['yes_cell'], $checkChar . ' YES');
            } elseif ($value === 'N') {
                $sheet->setCellValue($cells['no_cell'], $checkChar . ' NO');
            }
        }
    }

    /**
     * Page 4: "If YES, give details" — write detail value to cell only when corresponding Y/N is Y.
     */
    protected function fillPage4Details(Spreadsheet $spreadsheet, PersonalDataSheet $pds): void
    {
        $config = config('pds-excel.page4_details');
        if (! $config || empty($config['items'])) {
            return;
        }
        $sheetIndex = $config['sheet'] ?? 3;
        if ($spreadsheet->getSheetCount() <= $sheetIndex) {
            return;
        }
        $sheet = $spreadsheet->getSheet($sheetIndex);

        foreach ($config['items'] as $item) {
            $ynField = $item['yn_field'];
            $ynFields = is_array($ynField) ? $ynField : [$ynField];
            $show = false;
            foreach ($ynFields as $f) {
                $v = $pds->getAttribute($f);
                if ($v === 'Y' || $v === 'y') {
                    $show = true;
                    break;
                }
            }
            if (! $show) {
                continue;
            }
            $value = $pds->getAttribute($item['value_field']);
            if ($value !== null && $value !== '') {
                $sheet->setCellValue($item['cell'], (string) $value);
            }
        }
    }

    /**
     * Page 4: References — NAME (A52:A54), OFFICE/RESIDENTIAL ADDRESS (F52:F54), CONTACT (G52:G54).
     */
    protected function fillPage4References(Spreadsheet $spreadsheet, PersonalDataSheet $pds): void
    {
        $config = config('pds-excel.page4_references');
        if (! $config || empty($config['rows']) || empty($config['columns']) || empty($config['fields'])) {
            return;
        }
        $sheetIndex = $config['sheet'] ?? 3;
        if ($spreadsheet->getSheetCount() <= $sheetIndex) {
            return;
        }
        $sheet = $spreadsheet->getSheet($sheetIndex);
        $rows = $config['rows'];
        $columns = $config['columns'];
        $fields = $config['fields'];

        foreach ($rows as $i => $rowNum) {
            $fieldTriple = $fields[$i] ?? null;
            if ($fieldTriple === null) {
                continue;
            }
            $sheet->setCellValue($columns['name'] . $rowNum, $pds->getAttribute($fieldTriple[0]) ?? '');
            $sheet->setCellValue($columns['address'] . $rowNum, $pds->getAttribute($fieldTriple[1]) ?? '');
            $sheet->setCellValue($columns['contact'] . $rowNum, $pds->getAttribute($fieldTriple[2]) ?? '');
        }
    }

    /**
     * Page 4: Government Issued ID — D61, D62, D63.
     */
    protected function fillPage4GovtId(Spreadsheet $spreadsheet, PersonalDataSheet $pds): void
    {
        $config = config('pds-excel.page4_govt_id');
        if (! $config || empty($config['cells'])) {
            return;
        }
        $sheetIndex = $config['sheet'] ?? 3;
        if ($spreadsheet->getSheetCount() <= $sheetIndex) {
            return;
        }
        $sheet = $spreadsheet->getSheet($sheetIndex);
        foreach ($config['cells'] as $field => $cell) {
            $value = $pds->getAttribute($field);
            if ($value !== null && $value !== '') {
                $sheet->setCellValue($cell, (string) $value);
            }
        }
    }

    protected function addPhotoIfExists(Spreadsheet $spreadsheet, PersonalDataSheet $pds): void
    {
        $photoConfig = config('pds-excel.photo');
        if (! $photoConfig) {
            return;
        }
        $absolutePath = $this->photoService->absolutePath($pds->photo_path);
        if ($absolutePath === null || ! is_readable($absolutePath)) {
            return;
        }
        $sheetIndex = $photoConfig['sheet'];
        $cell = $photoConfig['cell'];
        if ($spreadsheet->getSheetCount() <= $sheetIndex) {
            return;
        }
        $sheet = $spreadsheet->getSheet($sheetIndex);
        $drawing = new Drawing();
        $drawing->setPath($absolutePath);
        $drawing->setCoordinates($cell);
        $drawing->setWorksheet($sheet);
    }
}

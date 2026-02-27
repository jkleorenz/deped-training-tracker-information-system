<?php

namespace App\Services;

use App\Models\PersonalDataSheet;
use App\Models\User;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf as PdfWriter;

/**
 * Service to convert PDS Excel template directly to PDF.
 * This service generates the Excel file and converts it directly to PDF
 * preserving the exact layout of the official CS Form 212 template.
 */
class PdsPrintService
{
    public function __construct(
        protected PdsExcelService $excelService,
        protected PdsPhotoService $photoService
    ) {}

    /**
     * Generate a print-ready PDF by converting the Excel file directly.
     * This preserves the exact layout of the Excel template.
     *
     * @param User $user The user whose PDS to print
     * @param PersonalDataSheet|null $pds The PDS model (optional, will load from user if null)
     * @return string Path to the generated PDF file
     */
    public function generatePrintPdf(User $user, ?PersonalDataSheet $pds = null): string
    {
        $pds = $pds ?? $user->personalDataSheet;

        if (! $pds) {
            throw new \RuntimeException('Personal Data Sheet not found for user.');
        }

        // Generate the Excel file using PdsExcelService
        $excelPath = $this->excelService->generate($pds);

        try {
            // Convert Excel directly to PDF using PhpSpreadsheet's PDF writer
            $pdfPath = $this->convertExcelToPdf($excelPath, $user->id);

            // Clean up the temp Excel file
            if (file_exists($excelPath)) {
                @unlink($excelPath);
            }

            return $pdfPath;
        } catch (\Exception $e) {
            // Clean up on error
            if (file_exists($excelPath)) {
                @unlink($excelPath);
            }
            throw $e;
        }
    }

    /**
     * Stream the print PDF directly to browser.
     * Converts the Excel file directly to PDF preserving exact layout.
     *
     * @param User $user The user whose PDS to print
     * @param PersonalDataSheet|null $pds The PDS model
     * @return Response
     */
    public function streamPrintPdf(User $user, ?PersonalDataSheet $pds = null): Response
    {
        $pds = $pds ?? $user->personalDataSheet;

        if (! $pds) {
            abort(404, 'Personal Data Sheet not found. Please complete the PDS first.');
        }

        // Generate the Excel file using PdsExcelService
        $excelPath = $this->excelService->generate($pds);

        try {
            // Convert Excel directly to PDF
            $pdfPath = $this->convertExcelToPdf($excelPath, $user->id);

            // Clean up the temp Excel file
            if (file_exists($excelPath)) {
                @unlink($excelPath);
            }

            $filename = 'personal_data_sheet_' . preg_replace('/[^a-z0-9._-]/', '_', strtolower($user->name)) . '.pdf';

            // Stream the PDF
            $content = file_get_contents($pdfPath);

            // Clean up the PDF file after reading
            @unlink($pdfPath);

            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            // Clean up on error
            if (file_exists($excelPath)) {
                @unlink($excelPath);
            }
            throw $e;
        }
    }

    /**
     * Convert an Excel file to PDF using PhpSpreadsheet's Dompdf writer.
     * This preserves the exact layout, formatting, and structure of the Excel file.
     *
     * @param string $excelPath Path to the Excel file
     * @param int $userId User ID for temp file naming
     * @return string Path to the generated PDF file
     */
    protected function convertExcelToPdf(string $excelPath, int $userId): string
    {
        if (! file_exists($excelPath)) {
            throw new \RuntimeException("Excel file not found: {$excelPath}");
        }

        // Set higher memory and time limits for PDF conversion
        set_time_limit(300);
        if (function_exists('ini_set')) {
            @ini_set('memory_limit', '512M');
        }

        // Load the spreadsheet
        $spreadsheet = IOFactory::load($excelPath);

        // Configure PDF writer
        $pdfPath = storage_path('app/temp-pds-print-' . $userId . '-' . uniqid() . '.pdf');
        $dir = dirname($pdfPath);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        // Use PhpSpreadsheet's built-in PDF writer with Dompdf
        $writer = new PdfWriter($spreadsheet);
        $writer->writeAllSheets();
        $writer->save($pdfPath);

        // Clean up spreadsheet from memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $pdfPath;
    }

    /**
     * Alternative method: Generate PDF from database data using Blade template.
     * Use this if direct Excel-to-PDF conversion has issues.
     *
     * @param User $user
     * @param PersonalDataSheet|null $pds
     * @return Response
     */
    public function streamPrintPdfFromTemplate(User $user, ?PersonalDataSheet $pds = null): Response
    {
        $pds = $pds ?? $user->personalDataSheet;

        if (! $pds) {
            abort(404, 'Personal Data Sheet not found. Please complete the PDS first.');
        }

        // Load all related data
        $pds->loadMissing([
            'civilServiceEligibilities',
            'workExperiences',
            'voluntaryWorks',
            'learningDevelopments',
        ]);

        // Prepare data for the print template
        $printData = $this->preparePrintData($user, $pds);

        // Generate PDF using DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pds.print', $printData);

        // Configure PDF for optimal printing
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('margin-top', '0.25in');
        $pdf->setOption('margin-right', '0.25in');
        $pdf->setOption('margin-bottom', '0.25in');
        $pdf->setOption('margin-left', '0.25in');
        $pdf->setOption('footer-right', 'CS FORM 212 (Revised 2017), Page [page] of [topage]');
        $pdf->setOption('footer-font-size', 7);
        $pdf->setOption('enable-local-file-access', true);

        $filename = 'personal_data_sheet_' . preg_replace('/[^a-z0-9._-]/', '_', strtolower($user->name)) . '.pdf';

        return $pdf->stream($filename, ['Attachment' => false]);
    }

    /**
     * Prepare all data needed for the print template.
     * This method organizes PDS data into a structured format for the view.
     *
     * @param User $user
     * @param PersonalDataSheet $pds
     * @return array
     */
    protected function preparePrintData(User $user, PersonalDataSheet $pds): array
    {
        // Get photo path if exists
        $photoPath = null;
        if ($pds->photo_path) {
            $absolutePath = $this->photoService->absolutePath($pds->photo_path);
            if ($absolutePath && file_exists($absolutePath)) {
                $photoPath = $absolutePath;
            }
        }

        // Format children data
        $children = $this->formatChildrenData($pds);

        // Format civil service eligibilities
        $eligibilities = $pds->civilServiceEligibilities->map(function ($item) {
            return [
                'type' => $item->eligibility_type,
                'rating' => $item->rating,
                'date_exam' => $this->formatDate($item->date_exam_conferment),
                'place' => $item->place_exam_conferment,
                'license_number' => $item->license_number,
                'license_valid_until' => $this->formatDate($item->license_valid_until),
            ];
        })->toArray();

        // Format work experiences
        $workExperiences = $pds->workExperiences->map(function ($item) {
            return [
                'from_date' => $this->formatDate($item->from_date),
                'to_date' => $item->to_date ? $this->formatDate($item->to_date) : 'PRESENT',
                'position' => $item->position_title,
                'department' => $item->department_agency,
                'status' => $item->status_of_appointment,
                'govt_service' => $item->govt_service_yn === 'Y' ? 'Y' : 'N',
            ];
        })->toArray();

        // Format voluntary works
        $voluntaryWorks = $pds->voluntaryWorks->map(function ($item) {
            return [
                'organization' => $item->conducted_sponsored_by,
                'from_date' => $this->formatDate($item->inclusive_dates_from),
                'to_date' => $item->inclusive_dates_to ? $this->formatDate($item->inclusive_dates_to) : '',
                'hours' => $item->number_of_hours,
                'position' => $item->position_nature_of_work,
            ];
        })->toArray();

        // Format learning and development
        $learningDevelopments = $pds->learningDevelopments->map(function ($item) {
            return [
                'title' => $item->title_of_ld,
                'type' => $item->type_of_ld,
                'type_specify' => $item->type_of_ld_specify,
                'hours' => $item->number_of_hours,
                'from_date' => $this->formatDate($item->inclusive_dates_from),
                'to_date' => $item->inclusive_dates_to ? $this->formatDate($item->inclusive_dates_to) : '',
                'organization' => $item->organization_name_address,
            ];
        })->toArray();

        // Format other information (split by newlines)
        $specialSkills = $this->splitByNewlines($pds->special_skills_hobbies);
        $nonAcademic = $this->splitByNewlines($pds->non_academic_distinctions);
        $memberships = $this->splitByNewlines($pds->membership_in_associations);

        // Page 4 questions
        $page4Questions = [
            'related_third_degree' => [
                'answer' => $pds->related_third_degree_yn,
                'details' => $pds->related_authority_details,
            ],
            'related_fourth_degree' => [
                'answer' => $pds->related_fourth_degree_yn,
                'details' => null,
            ],
            'admin_offense' => [
                'answer' => $pds->admin_offense_yn,
                'details' => $pds->admin_offense_details,
            ],
            'criminally_charged' => [
                'answer' => $pds->criminally_charged_yn,
                'date_filed' => $pds->criminally_charged_date_filed,
                'status' => $pds->criminally_charged_status,
            ],
            'convicted' => [
                'answer' => $pds->convicted_yn,
                'details' => $pds->convicted_details,
            ],
            'separated_from_service' => [
                'answer' => $pds->separated_from_service_yn,
                'details' => $pds->separated_from_service_details,
            ],
            'candidate_election' => [
                'answer' => $pds->candidate_election_yn,
                'details' => $pds->candidate_election_details,
            ],
            'resigned_campaign' => [
                'answer' => $pds->resigned_campaign_yn,
                'details' => $pds->resigned_campaign_details,
            ],
            'immigrant_resident' => [
                'answer' => $pds->immigrant_resident_yn,
                'details' => $pds->immigrant_resident_details,
            ],
            'indigenous_group' => [
                'answer' => $pds->indigenous_group_yn,
                'details' => $pds->indigenous_group_specify,
            ],
            'pwd' => [
                'answer' => $pds->pwd_yn,
                'id_no' => $pds->pwd_id_no,
            ],
            'solo_parent' => [
                'answer' => $pds->solo_parent_yn,
                'id_no' => $pds->solo_parent_id_no,
            ],
        ];

        // References
        $references = [
            [
                'name' => $pds->ref1_name,
                'address' => $pds->ref1_address,
                'contact' => $pds->ref1_contact,
            ],
            [
                'name' => $pds->ref2_name,
                'address' => $pds->ref2_address,
                'contact' => $pds->ref2_contact,
            ],
            [
                'name' => $pds->ref3_name,
                'address' => $pds->ref3_address,
                'contact' => $pds->ref3_contact,
            ],
        ];

        // Government ID
        $govtId = [
            'type' => $pds->govt_id_type,
            'number' => $pds->govt_id_number,
            'place_date_issue' => $pds->govt_id_place_date_issue,
        ];

        return [
            'user' => $user,
            'pds' => $pds,
            'photoPath' => $photoPath,

            // Personal Information
            'surname' => $pds->surname,
            'firstName' => $pds->first_name,
            'middleName' => $pds->middle_name,
            'nameExtension' => $pds->name_extension,
            'dateOfBirth' => $this->formatDate($pds->date_of_birth),
            'placeOfBirth' => $pds->place_of_birth,
            'sex' => $this->formatSex($pds->sex),
            'civilStatus' => $this->formatCivilStatus($pds->civil_status, $pds->civil_status_other),
            'height' => $pds->height,
            'weight' => $pds->weight,
            'bloodType' => $pds->blood_type,

            // Government IDs
            'gsisId' => $pds->umid_id,
            'pagibigId' => $pds->pagibig_id,
            'philhealthNo' => $pds->philhealth_no,
            'philsysNumber' => $pds->philsys_number,
            'tinNo' => $pds->tin_no,
            'agencyEmployeeNo' => $pds->agency_employee_no,

            // Citizenship
            'citizenship' => $this->formatCitizenship($pds),

            // Addresses
            'residentialAddress' => $this->formatAddress(
                $pds->residential_house_no,
                $pds->residential_street,
                $pds->residential_subdivision,
                $pds->residential_barangay,
                $pds->residential_city,
                $pds->residential_province,
                $pds->residential_zip
            ),
            'permanentAddress' => $this->formatAddress(
                $pds->permanent_house_no,
                $pds->permanent_street,
                $pds->permanent_subdivision,
                $pds->permanent_barangay,
                $pds->permanent_city,
                $pds->permanent_province,
                $pds->permanent_zip
            ),

            // Contact
            'telephone' => $pds->telephone,
            'mobile' => $pds->mobile,
            'email' => $pds->email_address,

            // Family Background
            'spouse' => [
                'surname' => $pds->spouse_surname,
                'firstName' => $pds->spouse_first_name,
                'middleName' => $pds->spouse_middle_name,
                'nameExtension' => $pds->spouse_name_extension,
                'occupation' => $pds->spouse_occupation,
                'employer' => $pds->spouse_employer_business_name,
                'businessAddress' => $pds->spouse_business_address,
                'telephone' => $pds->spouse_telephone,
            ],
            'children' => $children,
            'father' => [
                'surname' => $pds->father_surname,
                'firstName' => $pds->father_first_name,
                'middleName' => $pds->father_middle_name,
                'nameExtension' => $pds->father_name_extension,
            ],
            'mother' => [
                'surname' => $pds->mother_surname,
                'firstName' => $pds->mother_first_name,
                'middleName' => $pds->mother_middle_name,
                'nameExtension' => $pds->mother_name_extension,
            ],

            // Educational Background
            'education' => [
                'elementary' => $this->formatEducation($pds, 'elem'),
                'secondary' => $this->formatEducation($pds, 'secondary'),
                'vocational' => $this->formatEducation($pds, 'voc'),
                'college' => $this->formatEducation($pds, 'college'),
                'graduate' => $this->formatEducation($pds, 'grad'),
            ],

            // Repeating sections
            'eligibilities' => $eligibilities,
            'workExperiences' => $workExperiences,
            'voluntaryWorks' => $voluntaryWorks,
            'learningDevelopments' => $learningDevelopments,

            // Other Information
            'specialSkills' => $specialSkills,
            'nonAcademic' => $nonAcademic,
            'memberships' => $memberships,

            // Page 4
            'page4Questions' => $page4Questions,
            'references' => $references,
            'govtId' => $govtId,
            'dateAccomplished' => $this->formatDate($pds->date_accomplished),
        ];
    }

    /**
     * Format children data from the stored format.
     *
     * @param PersonalDataSheet $pds
     * @return array
     */
    protected function formatChildrenData(PersonalDataSheet $pds): array
    {
        $children = [];

        // Try children_data array first
        if (! empty($pds->children_data) && is_array($pds->children_data)) {
            foreach ($pds->children_data as $child) {
                $children[] = [
                    'name' => $child['name'] ?? '',
                    'dob' => isset($child['dob']) ? $this->formatDate($child['dob']) : '',
                ];
            }
        } elseif (! empty($pds->children_names)) {
            // Fallback to children_names string
            $names = array_filter(array_map('trim', explode("\n", $pds->children_names)));
            foreach ($names as $name) {
                $children[] = [
                    'name' => $name,
                    'dob' => '',
                ];
            }
        }

        return $children;
    }

    /**
     * Format education data for a specific level.
     *
     * @param PersonalDataSheet $pds
     * @param string $prefix
     * @return array
     */
    protected function formatEducation(PersonalDataSheet $pds, string $prefix): array
    {
        return [
            'school' => $pds->getAttribute("{$prefix}_school"),
            'degree' => $pds->getAttribute("{$prefix}_degree_course"),
            'periodFrom' => $pds->getAttribute("{$prefix}_period_from"),
            'periodTo' => $pds->getAttribute("{$prefix}_period_to"),
            'highestLevel' => $pds->getAttribute("{$prefix}_highest_level_units"),
            'yearGraduated' => $pds->getAttribute("{$prefix}_year_graduated"),
            'honors' => $pds->getAttribute("{$prefix}_scholarship_honors"),
        ];
    }

    /**
     * Format address components into a single string.
     *
     * @param string|null ...$parts
     * @return string
     */
    protected function formatAddress(?string ...$parts): string
    {
        $parts = array_filter($parts, fn ($p) => ! empty($p));

        return implode(', ', $parts);
    }

    /**
     * Format citizenship display.
     *
     * @param PersonalDataSheet $pds
     * @return string
     */
    protected function formatCitizenship(PersonalDataSheet $pds): string
    {
        if ($pds->citizenship === 'filipino') {
            return 'Filipino';
        }

        if ($pds->citizenship === 'dual') {
            $type = $pds->dual_citizenship_type === 'by_birth' ? 'by birth' : 'by naturalization';
            $country = $pds->dual_citizenship_country ? " ({$pds->dual_citizenship_country})" : '';

            return "Dual citizenship {$type}{$country}";
        }

        return '';
    }

    /**
     * Format sex display.
     *
     * @param string|null $sex
     * @return string
     */
    protected function formatSex(?string $sex): string
    {
        return match ($sex) {
            'male' => 'Male',
            'female' => 'Female',
            default => '',
        };
    }

    /**
     * Format civil status display.
     *
     * @param string|null $status
     * @param string|null $other
     * @return string
     */
    protected function formatCivilStatus(?string $status, ?string $other): string
    {
        $formatted = match ($status) {
            'single' => 'Single',
            'married' => 'Married',
            'widowed' => 'Widowed',
            'separated' => 'Separated',
            'other' => $other ? "Other ({$other})" : 'Other',
            default => '',
        };

        return $formatted;
    }

    /**
     * Format a date value.
     *
     * @param mixed $date
     * @param string $format
     * @return string
     */
    protected function formatDate(mixed $date, string $format = 'm/d/Y'): string
    {
        if (! $date) {
            return '';
        }

        if ($date instanceof \DateTimeInterface) {
            return $date->format($format);
        }

        try {
            return \Carbon\Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return (string) $date;
        }
    }

    /**
     * Split text by newlines and return array of non-empty lines.
     *
     * @param string|null $text
     * @return array
     */
    protected function splitByNewlines(?string $text): array
    {
        if (empty($text)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode("\n", $text))));
    }

    /**
     * Extract data from a generated Excel file using the cell mappings from config.
     * This ensures the PDF data matches exactly what's in the Excel template.
     *
     * @param string $excelPath Path to the Excel file
     * @return array Extracted data from the Excel file
     */
    protected function extractDataFromExcel(string $excelPath): array
    {
        if (! file_exists($excelPath)) {
            throw new \RuntimeException("Excel file not found: {$excelPath}");
        }

        $spreadsheet = IOFactory::load($excelPath);
        $extractedData = [];

        // Extract specific cell values based on config mapping
        $cellMappings = config('pds-excel.cells', []);

        foreach ($cellMappings as $field => $config) {
            if ($config === null) {
                continue;
            }

            $sheetIndex = $config['sheet'];
            $cell = $config['cell'];

            if ($spreadsheet->getSheetCount() > $sheetIndex) {
                $sheet = $spreadsheet->getSheet($sheetIndex);
                $value = $sheet->getCell($cell)->getValue();
                $extractedData[$field] = $this->formatExcelValue($value);
            }
        }

        // Extract repeating sections data
        $extractedData['civil_service'] = $this->extractRepeatingSection($spreadsheet, 'civil_service');
        $extractedData['work_experience'] = $this->extractRepeatingSection($spreadsheet, 'work_experience');
        $extractedData['voluntary_work'] = $this->extractRepeatingSection($spreadsheet, 'voluntary_work');
        $extractedData['learning_development'] = $this->extractRepeatingSection($spreadsheet, 'learning_development');
        $extractedData['children'] = $this->extractChildrenData($spreadsheet);
        $extractedData['other_information'] = $this->extractOtherInformation($spreadsheet);
        $extractedData['page4_yn'] = $this->extractPage4YesNo($spreadsheet);
        $extractedData['page4_details'] = $this->extractPage4Details($spreadsheet);
        $extractedData['page4_references'] = $this->extractPage4References($spreadsheet);
        $extractedData['page4_govt_id'] = $this->extractPage4GovtId($spreadsheet);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $extractedData;
    }

    /**
     * Format a value from Excel for display.
     *
     * @param mixed $value
     * @return string
     */
    protected function formatExcelValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('m/d/Y');
        }

        return (string) $value;
    }

    /**
     * Extract data from a repeating section in the Excel file.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @param string $sectionKey
     * @return array
     */
    protected function extractRepeatingSection(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet, string $sectionKey): array
    {
        $config = config("pds-excel.{$sectionKey}");
        if (! $config || empty($config['columns'])) {
            return [];
        }

        $sheetIndex = $config['sheet'];
        if ($spreadsheet->getSheetCount() <= $sheetIndex) {
            return [];
        }

        $sheet = $spreadsheet->getSheet($sheetIndex);
        $startRow = $config['start_row'];
        $maxRows = $config['max_rows'] ?? 20;
        $columns = $config['columns'];
        $data = [];

        for ($i = 0; $i < $maxRows; $i++) {
            $row = $startRow + $i;
            $rowData = [];
            $hasData = false;

            foreach ($columns as $field => $col) {
                $value = $sheet->getCell($col . $row)->getValue();
                $rowData[$field] = $this->formatExcelValue($value);
                if (! empty($rowData[$field])) {
                    $hasData = true;
                }
            }

            if ($hasData) {
                $data[] = $rowData;
            }
        }

        return $data;
    }

    /**
     * Extract children data from Excel.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @return array
     */
    protected function extractChildrenData(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): array
    {
        $config = config('pds-excel.children');
        if (! $config) {
            return [];
        }

        $sheetIndex = $config['sheet'];
        if ($spreadsheet->getSheetCount() <= $sheetIndex) {
            return [];
        }

        $sheet = $spreadsheet->getSheet($sheetIndex);
        $startRow = $config['start_row'];
        $maxRows = $config['max_rows'] ?? 12;
        $nameCol = $config['name_column'];
        $dobCol = $config['dob_column'];
        $children = [];

        for ($i = 0; $i < $maxRows; $i++) {
            $row = $startRow + $i;
            $name = $this->formatExcelValue($sheet->getCell($nameCol . $row)->getValue());
            $dob = $this->formatExcelValue($sheet->getCell($dobCol . $row)->getValue());

            if (! empty($name) || ! empty($dob)) {
                $children[] = [
                    'name' => $name,
                    'dob' => $dob,
                ];
            }
        }

        return $children;
    }

    /**
     * Extract other information (special skills, etc.) from Excel.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @return array
     */
    protected function extractOtherInformation(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): array
    {
        $config = config('pds-excel.other_information');
        if (! $config || empty($config['columns'])) {
            return [];
        }

        $sheetIndex = $config['sheet'];
        if ($spreadsheet->getSheetCount() <= $sheetIndex) {
            return [];
        }

        $sheet = $spreadsheet->getSheet($sheetIndex);
        $startRow = $config['start_row'];
        $maxRows = $config['max_rows'] ?? 7;
        $columns = $config['columns'];
        $data = [];

        foreach ($columns as $field => $col) {
            $data[$field] = [];
            for ($i = 0; $i < $maxRows; $i++) {
                $row = $startRow + $i;
                $value = $this->formatExcelValue($sheet->getCell($col . $row)->getValue());
                if (! empty($value)) {
                    $data[$field][] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * Extract Page 4 Yes/No answers from Excel.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @return array
     */
    protected function extractPage4YesNo(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): array
    {
        $config = config('pds-excel.page4_yn');
        if (! $config || empty($config['questions'])) {
            return [];
        }

        $sheetIndex = $config['sheet'] ?? 3;
        if ($spreadsheet->getSheetCount() <= $sheetIndex) {
            return [];
        }

        $sheet = $spreadsheet->getSheet($sheetIndex);
        $checkChar = $config['check_char'] ?? '✓';
        $answers = [];

        foreach ($config['questions'] as $field => $cells) {
            $yesValue = $sheet->getCell($cells['yes_cell'])->getValue();
            $noValue = $sheet->getCell($cells['no_cell'])->getValue();

            $yesStr = $this->formatExcelValue($yesValue);
            $noStr = $this->formatExcelValue($noValue);

            if (str_contains($yesStr, $checkChar) || str_contains($yesStr, 'YES')) {
                $answers[$field] = 'Y';
            } elseif (str_contains($noStr, $checkChar) || str_contains($noStr, 'NO')) {
                $answers[$field] = 'N';
            } else {
                $answers[$field] = '';
            }
        }

        return $answers;
    }

    /**
     * Extract Page 4 details from Excel.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @return array
     */
    protected function extractPage4Details(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): array
    {
        $config = config('pds-excel.page4_details');
        if (! $config || empty($config['items'])) {
            return [];
        }

        $sheetIndex = $config['sheet'] ?? 3;
        if ($spreadsheet->getSheetCount() <= $sheetIndex) {
            return [];
        }

        $sheet = $spreadsheet->getSheet($sheetIndex);
        $details = [];

        foreach ($config['items'] as $item) {
            $valueField = $item['value_field'];
            $cell = $item['cell'];
            $details[$valueField] = $this->formatExcelValue($sheet->getCell($cell)->getValue());
        }

        return $details;
    }

    /**
     * Extract Page 4 references from Excel.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @return array
     */
    protected function extractPage4References(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): array
    {
        $config = config('pds-excel.page4_references');
        if (! $config || empty($config['rows']) || empty($config['columns'])) {
            return [];
        }

        $sheetIndex = $config['sheet'] ?? 3;
        if ($spreadsheet->getSheetCount() <= $sheetIndex) {
            return [];
        }

        $sheet = $spreadsheet->getSheet($sheetIndex);
        $rows = $config['rows'];
        $columns = $config['columns'];
        $references = [];

        foreach ($rows as $i => $rowNum) {
            $references[] = [
                'name' => $this->formatExcelValue($sheet->getCell($columns['name'] . $rowNum)->getValue()),
                'address' => $this->formatExcelValue($sheet->getCell($columns['address'] . $rowNum)->getValue()),
                'contact' => $this->formatExcelValue($sheet->getCell($columns['contact'] . $rowNum)->getValue()),
            ];
        }

        return $references;
    }

    /**
     * Extract Page 4 Government ID from Excel.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @return array
     */
    protected function extractPage4GovtId(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): array
    {
        $config = config('pds-excel.page4_govt_id');
        if (! $config || empty($config['cells'])) {
            return [];
        }

        $sheetIndex = $config['sheet'] ?? 3;
        if ($spreadsheet->getSheetCount() <= $sheetIndex) {
            return [];
        }

        $sheet = $spreadsheet->getSheet($sheetIndex);
        $govtId = [];

        foreach ($config['cells'] as $field => $cell) {
            $govtId[$field] = $this->formatExcelValue($sheet->getCell($cell)->getValue());
        }

        return $govtId;
    }

    /**
     * Prepare print data combining Excel-extracted data with database data.
     *
     * @param User $user
     * @param PersonalDataSheet $pds
     * @param array $excelData
     * @return array
     */
    protected function preparePrintDataFromExcel(User $user, PersonalDataSheet $pds, array $excelData): array
    {
        // Get photo path if exists
        $photoPath = null;
        if ($pds->photo_path) {
            $absolutePath = $this->photoService->absolutePath($pds->photo_path);
            if ($absolutePath && file_exists($absolutePath)) {
                $photoPath = $absolutePath;
            }
        }

        // Format children data from Excel
        $children = [];
        foreach ($excelData['children'] ?? [] as $child) {
            $children[] = [
                'name' => $child['name'] ?? '',
                'dob' => $child['dob'] ?? '',
            ];
        }

        // Format civil service eligibilities from Excel
        $eligibilities = [];
        foreach ($excelData['civil_service'] ?? [] as $item) {
            $eligibilities[] = [
                'type' => $item['eligibility_type'] ?? '',
                'rating' => $item['rating'] ?? '',
                'date_exam' => $item['date_exam_conferment'] ?? '',
                'place' => $item['place_exam_conferment'] ?? '',
                'license_number' => $item['license_number'] ?? '',
                'license_valid_until' => $item['license_valid_until'] ?? '',
            ];
        }

        // Format work experiences from Excel
        $workExperiences = [];
        foreach ($excelData['work_experience'] ?? [] as $item) {
            $workExperiences[] = [
                'from_date' => $item['from_date'] ?? '',
                'to_date' => $item['to_date'] ?? '',
                'position' => $item['position_title'] ?? '',
                'department' => $item['department_agency'] ?? '',
                'status' => $item['status_of_appointment'] ?? '',
                'govt_service' => $item['govt_service_yn'] ?? '',
            ];
        }

        // Format voluntary works from Excel
        $voluntaryWorks = [];
        foreach ($excelData['voluntary_work'] ?? [] as $item) {
            $voluntaryWorks[] = [
                'organization' => $item['conducted_sponsored_by'] ?? '',
                'from_date' => $item['inclusive_dates_from'] ?? '',
                'to_date' => $item['inclusive_dates_to'] ?? '',
                'hours' => $item['number_of_hours'] ?? '',
                'position' => $item['position_nature_of_work'] ?? '',
            ];
        }

        // Format learning and development from Excel
        $learningDevelopments = [];
        foreach ($excelData['learning_development'] ?? [] as $item) {
            $learningDevelopments[] = [
                'title' => $item['title_of_ld'] ?? '',
                'type' => $item['type_of_ld'] ?? '',
                'hours' => $item['number_of_hours'] ?? '',
                'from_date' => $item['inclusive_dates_from'] ?? '',
                'to_date' => $item['inclusive_dates_to'] ?? '',
                'organization' => $item['organization_name_address'] ?? '',
            ];
        }

        // Other information from Excel
        $otherInfo = $excelData['other_information'] ?? [];
        $specialSkills = $otherInfo['special_skills_hobbies'] ?? [];
        $nonAcademic = $otherInfo['non_academic_distinctions'] ?? [];
        $memberships = $otherInfo['membership_in_associations'] ?? [];

        // Page 4 questions from Excel
        $page4Yn = $excelData['page4_yn'] ?? [];
        $page4Details = $excelData['page4_details'] ?? [];
        $page4Questions = [
            'related_third_degree' => [
                'answer' => $page4Yn['related_third_degree_yn'] ?? '',
                'details' => $page4Details['related_authority_details'] ?? '',
            ],
            'related_fourth_degree' => [
                'answer' => $page4Yn['related_fourth_degree_yn'] ?? '',
                'details' => null,
            ],
            'admin_offense' => [
                'answer' => $page4Yn['admin_offense_yn'] ?? '',
                'details' => $page4Details['admin_offense_details'] ?? '',
            ],
            'criminally_charged' => [
                'answer' => $page4Yn['criminally_charged_yn'] ?? '',
                'date_filed' => $page4Details['criminally_charged_date_filed'] ?? '',
                'status' => $page4Details['criminally_charged_status'] ?? '',
            ],
            'convicted' => [
                'answer' => $page4Yn['convicted_yn'] ?? '',
                'details' => $page4Details['convicted_details'] ?? '',
            ],
            'separated_from_service' => [
                'answer' => $page4Yn['separated_from_service_yn'] ?? '',
                'details' => $page4Details['separated_from_service_details'] ?? '',
            ],
            'candidate_election' => [
                'answer' => $page4Yn['candidate_election_yn'] ?? '',
                'details' => $page4Details['candidate_election_details'] ?? '',
            ],
            'resigned_campaign' => [
                'answer' => $page4Yn['resigned_campaign_yn'] ?? '',
                'details' => $page4Details['resigned_campaign_details'] ?? '',
            ],
            'immigrant_resident' => [
                'answer' => $page4Yn['immigrant_resident_yn'] ?? '',
                'details' => $page4Details['immigrant_resident_details'] ?? '',
            ],
            'indigenous_group' => [
                'answer' => $page4Yn['indigenous_group_yn'] ?? '',
                'details' => $page4Details['indigenous_group_specify'] ?? '',
            ],
            'pwd' => [
                'answer' => $page4Yn['pwd_yn'] ?? '',
                'id_no' => $page4Details['pwd_id_no'] ?? '',
            ],
            'solo_parent' => [
                'answer' => $page4Yn['solo_parent_yn'] ?? '',
                'id_no' => $page4Details['solo_parent_id_no'] ?? '',
            ],
        ];

        // References from Excel
        $references = $excelData['page4_references'] ?? [
            ['name' => '', 'address' => '', 'contact' => ''],
            ['name' => '', 'address' => '', 'contact' => ''],
            ['name' => '', 'address' => '', 'contact' => ''],
        ];

        // Government ID from Excel
        $govtIdExcel = $excelData['page4_govt_id'] ?? [];
        $govtId = [
            'type' => $govtIdExcel['govt_id_type'] ?? '',
            'number' => $govtIdExcel['govt_id_number'] ?? '',
            'place_date_issue' => $govtIdExcel['govt_id_place_date_issue'] ?? '',
        ];

        return [
            'user' => $user,
            'pds' => $pds,
            'photoPath' => $photoPath,

            // Personal Information from Excel
            'surname' => $excelData['surname'] ?? '',
            'firstName' => $excelData['first_name'] ?? '',
            'middleName' => $excelData['middle_name'] ?? '',
            'nameExtension' => $excelData['name_extension'] ?? '',
            'dateOfBirth' => $excelData['date_of_birth'] ?? '',
            'placeOfBirth' => $excelData['place_of_birth'] ?? '',
            'sex' => $this->formatSex($excelData['sex'] ?? ''),
            'civilStatus' => $this->formatCivilStatus($excelData['civil_status'] ?? '', $excelData['civil_status_other'] ?? ''),
            'height' => $excelData['height'] ?? '',
            'weight' => $excelData['weight'] ?? '',
            'bloodType' => $excelData['blood_type'] ?? '',

            // Government IDs from Excel
            'gsisId' => $excelData['umid_id'] ?? '',
            'pagibigId' => $excelData['pagibig_id'] ?? '',
            'philhealthNo' => $excelData['philhealth_no'] ?? '',
            'philsysNumber' => $excelData['philsys_number'] ?? '',
            'tinNo' => $excelData['tin_no'] ?? '',
            'agencyEmployeeNo' => $excelData['agency_employee_no'] ?? '',

            // Citizenship from Excel
            'citizenship' => $this->formatCitizenshipFromExcel($excelData),

            // Addresses from Excel
            'residentialAddress' => $this->formatAddressFromExcel($excelData, 'residential'),
            'permanentAddress' => $this->formatAddressFromExcel($excelData, 'permanent'),

            // Contact from Excel
            'telephone' => $excelData['telephone'] ?? '',
            'mobile' => $excelData['mobile'] ?? '',
            'email' => $excelData['email_address'] ?? '',

            // Family Background from Excel
            'spouse' => [
                'surname' => $excelData['spouse_surname'] ?? '',
                'firstName' => $excelData['spouse_first_name'] ?? '',
                'middleName' => $excelData['spouse_middle_name'] ?? '',
                'nameExtension' => $excelData['spouse_name_extension'] ?? '',
                'occupation' => $excelData['spouse_occupation'] ?? '',
                'employer' => $excelData['spouse_employer_business_name'] ?? '',
                'businessAddress' => $excelData['spouse_business_address'] ?? '',
                'telephone' => $excelData['spouse_telephone'] ?? '',
            ],
            'children' => $children,
            'father' => [
                'surname' => $excelData['father_surname'] ?? '',
                'firstName' => $excelData['father_first_name'] ?? '',
                'middleName' => $excelData['father_middle_name'] ?? '',
                'nameExtension' => $excelData['father_name_extension'] ?? '',
            ],
            'mother' => [
                'surname' => $excelData['mother_surname'] ?? '',
                'firstName' => $excelData['mother_first_name'] ?? '',
                'middleName' => $excelData['mother_middle_name'] ?? '',
                'nameExtension' => $excelData['mother_name_extension'] ?? '',
            ],

            // Educational Background from Excel
            'education' => [
                'elementary' => $this->formatEducationFromExcel($excelData, 'elem'),
                'secondary' => $this->formatEducationFromExcel($excelData, 'secondary'),
                'vocational' => $this->formatEducationFromExcel($excelData, 'voc'),
                'college' => $this->formatEducationFromExcel($excelData, 'college'),
                'graduate' => $this->formatEducationFromExcel($excelData, 'grad'),
            ],

            // Repeating sections from Excel
            'eligibilities' => $eligibilities,
            'workExperiences' => $workExperiences,
            'voluntaryWorks' => $voluntaryWorks,
            'learningDevelopments' => $learningDevelopments,

            // Other Information from Excel
            'specialSkills' => $specialSkills,
            'nonAcademic' => $nonAcademic,
            'memberships' => $memberships,

            // Page 4 from Excel
            'page4Questions' => $page4Questions,
            'references' => $references,
            'govtId' => $govtId,
            'dateAccomplished' => $excelData['date_accomplished'] ?? '',
        ];
    }

    /**
     * Format citizenship from Excel data.
     *
     * @param array $excelData
     * @return string
     */
    protected function formatCitizenshipFromExcel(array $excelData): string
    {
        $citizenship = $excelData['citizenship'] ?? '';

        if (strcasecmp($citizenship, 'filipino') === 0 || strcasecmp($citizenship, 'Filipino') === 0) {
            return 'Filipino';
        }

        if (strcasecmp($citizenship, 'dual') === 0 || str_contains(strtolower($citizenship), 'dual')) {
            $type = $excelData['dual_citizenship_type'] ?? '';
            $typeStr = str_contains(strtolower($type), 'birth') ? 'by birth' : 'by naturalization';
            $country = $excelData['dual_citizenship_country'] ? " ({$excelData['dual_citizenship_country']})" : '';
            return "Dual citizenship {$typeStr}{$country}";
        }

        return $citizenship;
    }

    /**
     * Format address from Excel data.
     *
     * @param array $excelData
     * @param string $type
     * @return string
     */
    protected function formatAddressFromExcel(array $excelData, string $type): string
    {
        $parts = [
            $excelData["{$type}_house_no"] ?? '',
            $excelData["{$type}_street"] ?? '',
            $excelData["{$type}_subdivision"] ?? '',
            $excelData["{$type}_barangay"] ?? '',
            $excelData["{$type}_city"] ?? '',
            $excelData["{$type}_province"] ?? '',
            $excelData["{$type}_zip"] ?? '',
        ];

        return implode(', ', array_filter($parts));
    }

    /**
     * Format education from Excel data.
     *
     * @param array $excelData
     * @param string $prefix
     * @return array
     */
    protected function formatEducationFromExcel(array $excelData, string $prefix): array
    {
        return [
            'school' => $excelData["{$prefix}_school"] ?? '',
            'degree' => $excelData["{$prefix}_degree_course"] ?? '',
            'periodFrom' => $excelData["{$prefix}_period_from"] ?? '',
            'periodTo' => $excelData["{$prefix}_period_to"] ?? '',
            'highestLevel' => $excelData["{$prefix}_highest_level_units"] ?? '',
            'yearGraduated' => $excelData["{$prefix}_year_graduated"] ?? '',
            'honors' => $excelData["{$prefix}_scholarship_honors"] ?? '',
        ];
    }

    /**
     * Read data from an Excel file and return structured array.
     * This can be used to extract data from uploaded PDS Excel files.
     *
     * @param string $excelPath Path to the Excel file
     * @return array Structured data from the Excel file
     */
    public function readFromExcel(string $excelPath): array
    {
        if (! file_exists($excelPath)) {
            throw new \RuntimeException("Excel file not found: {$excelPath}");
        }

        $spreadsheet = IOFactory::load($excelPath);
        $data = [];

        // Get all sheets
        $sheetCount = $spreadsheet->getSheetCount();

        for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $data["sheet_{$i}"] = $sheet->toArray();
        }

        // Extract specific cell values based on config mapping
        $cellMappings = config('pds-excel.cells', []);
        $extractedData = [];

        foreach ($cellMappings as $field => $config) {
            if ($config === null) {
                continue;
            }

            $sheetIndex = $config['sheet'];
            $cell = $config['cell'];

            if (isset($data["sheet_{$sheetIndex}"])) {
                $sheet = $spreadsheet->getSheet($sheetIndex);
                $value = $sheet->getCell($cell)->getValue();
                $extractedData[$field] = $this->formatExcelValue($value);
            }
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return [
            'raw_sheets' => $data,
            'extracted_fields' => $extractedData,
        ];
    }

    /**
     * Map extracted Excel data to PDS model attributes.
     *
     * @param array $excelData Data extracted from readFromExcel
     * @return array Mapped data suitable for filling PDS model
     */
    public function mapExcelToPdsAttributes(array $excelData): array
    {
        $fields = $excelData['extracted_fields'] ?? [];
        $mapped = [];

        // Direct field mappings
        $directMappings = [
            'surname' => 'surname',
            'first_name' => 'first_name',
            'middle_name' => 'middle_name',
            'name_extension' => 'name_extension',
            'date_of_birth' => 'date_of_birth',
            'place_of_birth' => 'place_of_birth',
            'sex' => 'sex',
            'civil_status' => 'civil_status',
            'civil_status_other' => 'civil_status_other',
            'height' => 'height',
            'weight' => 'weight',
            'blood_type' => 'blood_type',
            'umid_id' => 'umid_id',
            'pagibig_id' => 'pagibig_id',
            'philhealth_no' => 'philhealth_no',
            'philsys_number' => 'philsys_number',
            'tin_no' => 'tin_no',
            'agency_employee_no' => 'agency_employee_no',
            'telephone' => 'telephone',
            'mobile' => 'mobile',
            'email_address' => 'email_address',
        ];

        foreach ($directMappings as $excelField => $pdsField) {
            if (isset($fields[$excelField])) {
                $mapped[$pdsField] = $fields[$excelField];
            }
        }

        // Parse dates
        $dateFields = ['date_of_birth'];
        foreach ($dateFields as $field) {
            if (isset($mapped[$field]) && ! empty($mapped[$field])) {
                try {
                    $mapped[$field] = \Carbon\Carbon::parse($mapped[$field])->format('Y-m-d');
                } catch (\Exception $e) {
                    // Keep original if parsing fails
                }
            }
        }

        return $mapped;
    }
}

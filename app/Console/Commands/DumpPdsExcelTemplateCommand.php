<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Finder\Finder;

class DumpPdsExcelTemplateCommand extends Command
{
    protected $signature = 'pds:dump-excel-template
                            {--limit=500 : Max number of cells to dump per sheet (0 = no limit)}';

    protected $description = 'Dump cell addresses and values from the PDS Excel template for mapping. Place template in storage/app/pds-templates/ first.';

    public function handle(): int
    {
        $dir = storage_path('app/pds-templates');
        if (! is_dir($dir)) {
            $this->error('Directory storage/app/pds-templates does not exist.');
            return self::FAILURE;
        }

        $finder = (new Finder())->in($dir)->files()->name('*.xlsx');
        $files = iterator_to_array($finder, false);
        if (count($files) === 0) {
            $this->error('No .xlsx file found in storage/app/pds-templates. Add the CS Form 212 template first.');
            return self::FAILURE;
        }

        $path = $files[0]->getPathname();
        $this->info('Reading: ' . $path . PHP_EOL);

        try {
            $spreadsheet = IOFactory::load($path);
        } catch (\Throwable $e) {
            $this->error('Failed to load file: ' . $e->getMessage());
            return self::FAILURE;
        }

        $limit = (int) $this->option('limit');

        foreach ($spreadsheet->getAllSheets() as $sheetIndex => $sheet) {
            $title = $sheet->getTitle();
            $this->line('========== Sheet #' . $sheetIndex . ': "' . $title . '" ==========');

            $rowIterator = $sheet->getRowIterator();
            $count = 0;
            foreach ($rowIterator as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $addr = $cell->getCoordinate();
                    $value = $cell->getValue();
                    if ($value !== null && (string) $value !== '') {
                        $this->line('  ' . $addr . ' => ' . $this->truncate((string) $value, 80));
                        $count++;
                        if ($limit > 0 && $count >= $limit) {
                            $this->line('  ... (limit ' . $limit . ' reached for this sheet)');
                            break 2;
                        }
                    }
                }
            }
            $this->line('');
        }

        $this->info('Done. Use the output above to fill config/pds-excel.php mapping (cells => field => sheet + cell).');
        return self::SUCCESS;
    }

    private function truncate(string $s, int $max): string
    {
        $s = str_replace(["\r", "\n"], ' ', $s);
        return strlen($s) > $max ? substr($s, 0, $max - 3) . '...' : $s;
    }
}

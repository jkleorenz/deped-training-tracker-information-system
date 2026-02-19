<?php

namespace App\Console\Commands;

use App\Models\PersonalDataSheet;
use App\Services\PdsExcelService;
use Illuminate\Console\Command;

class DebugPdsPhotoExportCommand extends Command
{
    protected $signature = 'pds:debug-photo {pds_id : The Personal Data Sheet ID to diagnose}';

    protected $description = 'Diagnose why the PDS photo may not appear in the Excel export (cell K51).';

    public function handle(PdsExcelService $excelService): int
    {
        $pdsId = $this->argument('pds_id');
        $pds = PersonalDataSheet::find($pdsId);

        if (! $pds) {
            $this->error("PDS with id [{$pdsId}] not found.");
            return self::FAILURE;
        }

        $diag = $excelService->diagnosePhotoExport($pds);

        $this->line('');
        $this->info('--- PDS Photo Export Diagnosis ---');
        $this->line('PDS ID: ' . $diag['pds_id']);
        $this->line('');

        $this->line('1. Database');
        $this->line('   photo_path: ' . ($diag['photo_path'] === null || $diag['photo_path'] === '' ? '(empty or null)' : $diag['photo_path']));
        $this->line('   normalized: ' . ($diag['normalized_path'] ?? '(n/a)'));
        $this->line('');

        $this->line('2. Resolved path (used by export)');
        $this->line('   path: ' . ($diag['resolved_absolute_path'] ?? '(null)'));
        $this->line('   exists: ' . ($diag['resolved_exists'] ? 'yes' : 'no'));
        $this->line('   readable: ' . ($diag['resolved_readable'] ? 'yes' : 'no'));
        $this->line('');

        if (! empty($diag['candidates_checked'])) {
            $this->line('3. Paths checked');
            foreach ($diag['candidates_checked'] as $c) {
                $this->line('   ' . $c['label']);
                $this->line('      ' . $c['path']);
                $this->line('      exists: ' . ($c['exists'] ? 'yes' : 'no') . ', readable: ' . ($c['readable'] ? 'yes' : 'no'));
            }
            $this->line('');
        }

        $this->line('4. Template');
        $this->line('   path: ' . ($diag['template_path'] ?? '(null)'));
        $this->line('   exists: ' . ($diag['template_exists'] ? 'yes' : 'no'));
        $this->line('   sheet_count: ' . $diag['sheet_count']);
        $this->line('   photo config sheet index: ' . ($diag['photo_config_sheet'] ?? 'n/a'));
        $this->line('   target sheet index (used): ' . ($diag['target_sheet_index'] ?? 'n/a'));
        $this->line('   target cell: ' . ($diag['target_cell'] ?? 'K51'));
        $this->line('');

        $would = $diag['would_add_photo'];
        $this->line('5. Result');
        if ($would) {
            $this->info('   Photo WOULD be added to the export at cell ' . ($diag['target_cell'] ?? 'K51') . ' on sheet index ' . $diag['target_sheet_index'] . '.');
            $this->line('   If it still does not appear, check that you are opening the correct sheet (e.g. 4th sheet if template has 4 sheets).');
        } else {
            $this->error('   Photo would NOT be added. Fix the issue above:');
            if ($diag['photo_path'] === null || $diag['photo_path'] === '') {
                $this->line('   → Upload and save a photo on the PDS edit page for this user.');
            }
            if (! $diag['resolved_readable']) {
                $this->line('   → Ensure the photo file exists and is readable at one of the paths shown above (e.g. storage/app/public/pds-photos/<id>.jpg or public/storage/...).');
            }
            if ($diag['sheet_count'] === 0 || ! $diag['template_exists']) {
                $this->line('   → Add a valid .xlsx template in storage/app/pds-templates/ (or set config pds-excel.template_path).');
            }
        }
        $this->line('');

        return self::SUCCESS;
    }
}

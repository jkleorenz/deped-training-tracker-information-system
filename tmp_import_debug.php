<?php
require __DIR__ . '/vendor/autoload.php';

use App\Imports\TrainingsImport;
use Illuminate\Support\Collection;

$rows = new Collection([
    new Collection([null, null, null, null, null, null, null, null, null]),
    new Collection([null, null, null, null, null, null, null, null, null]),
    new Collection(['Title', 'Type of L&D', 'Provider', 'Venue', 'Start Date', 'End Date', 'Hours', 'Attended Date', 'Remarks']),
    new Collection(['Training 1', 'Seminar', 'Org', 'City', '2024-01-01', '2024-01-02', 8, '2024-01-02', 'Note']),
]);

$import = new TrainingsImport();
$import->collection($rows);

var_export($import->getRows()->all());

<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Reads Excel rows for trainings. Expects headers: Title, Type, Provider, Venue,
 * Start Date, End Date, Hours, Attended Date, Remarks (same as single-user export).
 * Does not persist; controller creates Training records and attaches to user.
 */
class TrainingsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): Collection
    {
        return $rows;
    }
}

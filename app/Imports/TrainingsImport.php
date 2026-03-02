<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use RuntimeException;

/**
 * Reads Excel rows for trainings. Expects headers: Title, Type, Provider, Venue,
 * Start Date, End Date, Hours, Attended Date, Remarks (same as single-user export).
 * Does not persist; controller creates Training records and attaches to user.
 */
class TrainingsImport implements ToCollection
{
    private Collection $parsedRows;

    public function __construct()
    {
        $this->parsedRows = collect();
    }

    private const FIELD_ORDER = [
        'title',
        'type_of_ld',
        'type_of_ld_specify',
        'provider',
        'venue',
        'start_date',
        'end_date',
        'hours',
        'attended_date',
        'remarks',
    ];

    private const REQUIRED_FIELDS = ['title', 'start_date', 'end_date'];

    private const HEADER_ALIASES = [
        'title' => ['title', 'training title'],
        'type_of_ld' => ['type of ld', 'type of learning development', 'type', 'type of ld activity'],
        'type_of_ld_specify' => ['type of ld specify', 'type specify', 'type of learning development specify'],
        'provider' => ['provider', 'training provider', 'organizer'],
        'venue' => ['venue', 'location', 'place'],
        'start_date' => ['start date', 'date start', 'date from', 'from'],
        'end_date' => ['end date', 'date end', 'date to', 'to'],
        'hours' => ['hours', 'no of hours', 'number of hours', 'training hours'],
        'attended_date' => ['attended date', 'date attended', 'attendance date'],
        'remarks' => ['remarks', 'comments', 'notes'],
    ];

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            $this->parsedRows = collect();
            return;
        }

        [$headerIndex, $columnMap] = $this->detectHeaderRow($rows);

        if ($headerIndex === null) {
            throw new RuntimeException('Could not find the STA header row. Ensure the sheet contains the standard headers (Title, Type of L&D, Provider, Venue, Start Date, End Date, Hours, Attended Date, Remarks).');
        }

        $dataRows = $rows->slice($headerIndex + 1)->values();

        if ($dataRows->isEmpty()) {
            $this->parsedRows = collect();
            return;
        }

        $this->parsedRows = $dataRows
            ->map(function ($row, $offset) use ($columnMap, $headerIndex) {
                $rowArray = $this->normalizeRow($row);
                $assoc = ['__row_number' => $headerIndex + 2 + $offset];

                foreach (self::FIELD_ORDER as $field) {
                    $columnKey = $columnMap[$field] ?? null;
                    $assoc[$field] = $columnKey !== null ? ($rowArray[$columnKey] ?? null) : null;
                }

                $assoc['__raw'] = $rowArray;

                return $assoc;
            });
    }

    public function getRows(): Collection
    {
        return $this->parsedRows;
    }

    /**
     * @return array{0:int|null,1:array<string,int|string>}
     */
    private function detectHeaderRow(Collection $rows): array
    {
        foreach ($rows as $index => $row) {
            $rowArray = $this->normalizeRow($row);
            $columnMap = $this->buildHeaderMap($rowArray);
            if ($this->hasRequiredFields($columnMap)) {
                return [$index, $columnMap];
            }
        }

        return [null, []];
    }

    /**
     * @param  array<int|string, mixed>  $row
     * @return array<string, int|string>
     */
    private function buildHeaderMap(array $row): array
    {
        $map = [];

        foreach ($row as $columnKey => $value) {
            $fieldKey = $this->matchFieldKey($value);
            if ($fieldKey !== null && ! array_key_exists($fieldKey, $map)) {
                $map[$fieldKey] = $columnKey;
            }
        }

        return $map;
    }

    /**
     * @param  mixed  $value
     */
    private function matchFieldKey($value): ?string
    {
        $normalized = $this->normalizeHeaderValue($value);
        if ($normalized === null) {
            return null;
        }

        foreach (self::HEADER_ALIASES as $field => $aliases) {
            if (in_array($normalized, $aliases, true)) {
                return $field;
            }
        }

        return null;
    }

    private function hasRequiredFields(array $columnMap): bool
    {
        foreach (self::REQUIRED_FIELDS as $required) {
            if (! array_key_exists($required, $columnMap)) {
                return false;
            }
        }

        return true;
    }

    private function normalizeHeaderValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $string = trim((string) $value);
        if ($string === '') {
            return null;
        }

        $string = Str::lower($string);
        $string = str_replace('&', '', $string);
        $string = preg_replace('/[^a-z0-9]+/u', ' ', $string) ?? '';
        $string = preg_replace('/\s+/', ' ', $string) ?? '';

        $string = trim($string);

        return $string === '' ? null : $string;
    }

    /**
     * @param  mixed  $row
     * @return array<int|string, mixed>
     */
    private function normalizeRow($row): array
    {
        if ($row instanceof Collection) {
            return $row->toArray();
        }

        if (is_array($row)) {
            return $row;
        }

        if (is_object($row) && method_exists($row, 'toArray')) {
            return (array) $row->toArray();
        }

        return (array) $row;
    }
}

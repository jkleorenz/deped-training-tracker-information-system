<?php

namespace App\Http\Controllers;

use App\Imports\TrainingsImport;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class MyTrainingController extends Controller
{
    /** Standard display date format (e.g. 09 Feb 2026). */
    private const DATE_DISPLAY = 'd M Y';

    /** Allowed sort columns for whitelist. */
    private const SORTABLE_COLUMNS = ['start_date', 'title', 'hours'];

    /** Allowed Type of L&D filter values. */
    private const TYPE_OF_LD_VALUES = ['Managerial', 'Supervisory', 'Technical', 'Other'];

    /**
     * Format a date value (string, Carbon, or DateTime) to display format.
     */
    private function formatDateDisplay(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            return Carbon::parse($value)->format(self::DATE_DISPLAY);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Format a date value to Y-m-d for API/inputs.
     */
    private function formatDateForApi(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get the current user's trainings with search, filters, sort, and pagination.
     * Query params: q, type, year, min_hours, max_hours, sort, direction, page, per_page.
     */
    public function myRecord(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthenticated.', 'data' => []], 401);
            }

            $query = $user->trainings()->withPivot(['attended_date', 'remarks']);

            // Search: title, provider, venue
            if ($request->filled('q')) {
                $q = $request->input('q');
                $query->where(function ($qb) use ($q) {
                    $qb->where('trainings.title', 'like', '%' . $q . '%')
                        ->orWhere('trainings.provider', 'like', '%' . $q . '%')
                        ->orWhere('trainings.venue', 'like', '%' . $q . '%');
                });
            }

            // Type of L&D filter (whitelist)
            if ($request->filled('type_of_ld')) {
                $typeOfLd = $request->input('type_of_ld');
                if (in_array($typeOfLd, self::TYPE_OF_LD_VALUES, true)) {
                    $query->where('trainings.type_of_ld', $typeOfLd);
                }
            }

            // Year filter (start_date year)
            if ($request->filled('year')) {
                $year = (int) $request->input('year');
                if ($year >= 1900 && $year <= 2100) {
                    $query->whereYear('trainings.start_date', $year);
                }
            }

            // Hours range
            if ($request->filled('min_hours')) {
                $min = (int) $request->input('min_hours');
                if ($min >= 0) {
                    $query->where('trainings.hours', '>=', $min);
                }
            }
            if ($request->filled('max_hours')) {
                $max = (int) $request->input('max_hours');
                if ($max >= 0) {
                    $query->where('trainings.hours', '<=', $max);
                }
            }

            // Sort (whitelist); default start_date desc
            $sort = $request->input('sort', 'start_date');
            if (!in_array($sort, self::SORTABLE_COLUMNS, true)) {
                $sort = 'start_date';
            }
            $direction = strtolower($request->input('direction', 'desc'));
            if (!in_array($direction, ['asc', 'desc'], true)) {
                $direction = 'desc';
            }
            $query->orderBy('trainings.' . $sort, $direction);

            // Pagination
            $perPage = (int) $request->input('per_page', 10);
            if (!in_array($perPage, [10, 25, 50], true)) {
                $perPage = 10;
            }
            $perPage = min(max($perPage, 1), 100);
            $paginator = $query->paginate($perPage);

            $items = $paginator->getCollection()->map(function ($training) {
                return [
                    'id' => $training->id,
                    'title' => $training->title,
                    'type_of_ld' => $training->type_of_ld,
                    'type_of_ld_specify' => $training->type_of_ld_specify,
                    'provider' => $training->provider,
                    'venue' => $training->venue,
                    'start_date' => $this->formatDateForApi($training->start_date),
                    'end_date' => $this->formatDateForApi($training->end_date),
                    'start_date_display' => $this->formatDateDisplay($training->start_date),
                    'end_date_display' => $this->formatDateDisplay($training->end_date),
                    'hours' => $training->hours,
                    'attended_date' => $this->formatDateForApi($training->pivot?->attended_date),
                    'attended_date_display' => $this->formatDateDisplay($training->pivot?->attended_date),
                    'remarks' => $training->pivot?->remarks,
                ];
            });

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'employee_id' => $user->employee_id,
                    'designation' => $user->designation,
                    'department' => $user->department,
                    'school' => $user->school,
                ],
                'data' => $items->values()->all(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                    'sort' => $sort,
                    'direction' => $direction,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'message' => 'Error loading trainings.',
                'data' => [],
            ], 500);
        }
    }

    /**
     * List all trainings (for dropdown when adding self to existing).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Training::query()->orderBy('start_date', 'desc');

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('provider', 'like', "%{$s}%")
                    ->orWhere('type_of_ld', 'like', "%{$s}%");
            });
        }

        $trainings = $query->get(['id', 'title', 'type_of_ld', 'type_of_ld_specify', 'provider', 'venue', 'start_date', 'end_date', 'hours']);
        return response()->json(['data' => $trainings]);
    }

    /**
     * Attach current user to an existing training.
     */
    public function attach(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'training_id' => ['required', 'integer', 'exists:trainings,id'],
            'attended_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $training = Training::findOrFail($validated['training_id']);
        $training->users()->syncWithoutDetaching([
            $request->user()->id => [
                'attended_date' => $validated['attended_date'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
            ],
        ]);

        return response()->json(['message' => 'Training added to your record.', 'data' => $training->fresh()]);
    }

    /**
     * Create a new training and attach current user.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type_of_ld' => ['nullable', 'string', 'max:100'],
            'type_of_ld_specify' => ['nullable', 'string', 'max:100'],
            'provider' => ['nullable', 'string', 'max:255'],
            'venue' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'hours' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'certificate_number' => ['nullable', 'string', 'max:100'],
            'attended_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $attendedDate = $validated['attended_date'] ?? $validated['start_date'] ?? null;
        $remarks = $validated['remarks'] ?? null;
        unset($validated['attended_date'], $validated['remarks']);

        $training = Training::create($validated);
        $training->users()->attach($request->user()->id, [
            'attended_date' => $attendedDate,
            'remarks' => $remarks,
        ]);

        return response()->json(['message' => 'Training added to your record.', 'data' => $training->load('users')], 201);
    }

    /**
     * Update current user's attendance (pivot) for a training.
     */
    public function updatePivot(Request $request, Training $training): JsonResponse
    {
        $user = $request->user();
        if (!$user->trainings()->where('trainings.id', $training->id)->exists()) {
            return response()->json(['message' => 'You are not assigned to this training.'], 404);
        }

        $validated = $request->validate([
            'attended_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $user->trainings()->updateExistingPivot($training->id, [
            'attended_date' => $validated['attended_date'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return response()->json(['message' => 'Attendance updated.', 'data' => $training->fresh()]);
    }

    /**
     * Remove current user from a training (remove from my record).
     */
    public function detach(Training $training): JsonResponse
    {
        $request = request();
        $user = $request->user();
        if (!$user->trainings()->where('trainings.id', $training->id)->exists()) {
            return response()->json(['message' => 'You are not assigned to this training.'], 404);
        }

        $user->trainings()->detach($training->id);
        return response()->json(['message' => 'Removed from your record.']);
    }

    /**
     * Import trainings from Excel for the current user.
     * Excel columns: Title, Type of L&D, Provider, Venue, Start Date, End Date, Hours, Attended Date, Remarks.
     */
    public function import(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'max:10240'],
        ], [
            'file.required' => 'Please select an Excel file to upload.',
            'file.file' => 'The upload must be a file.',
            'file.max' => 'The file may not be larger than 10 MB.',
        ]);

        // Accept .xlsx and .xls by extension (avoid MIME sniffing issues)
        $file = $request->file('file');
        if ($file) {
            $ext = strtolower($file->getClientOriginalExtension());
            if (! in_array($ext, ['xlsx', 'xls'], true)) {
                $validator->errors()->add('file', 'The file must be an Excel file (.xlsx or .xls).');
            }
        }

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $rows = Excel::toCollection(new TrainingsImport(), $file)->first();
        if (! $rows || $rows->isEmpty()) {
            return response()->json([
                'message' => 'The file has no data rows.',
                'errors' => ['file' => ['No data rows found. Ensure the first row has headers and there is at least one data row.']],
            ], 422);
        }

        $imported = 0;
        $duplicates = 0;
        $errors = [];

        $parseDate = function ($value) {
            if ($value === null || $value === '') {
                return null;
            }
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value);
            }
            $value = is_string($value) ? trim($value) : $value;
            // Excel date serial (e.g. 44927) – 1 = 1900-01-01
            if (is_numeric($value)) {
                $serial = (int) (float) $value;
                if ($serial >= 1 && $serial <= 2958465) {
                    return Carbon::create(1899, 12, 31)->addDays($serial);
                }
            }
            if (is_string($value) && Carbon::hasFormat($value, 'Y-m-d')) {
                return Carbon::createFromFormat('Y-m-d', $value);
            }
            try {
                return Carbon::parse($value);
            } catch (\Throwable $e) {
                return null;
            }
        };

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // 1-based + header row
            $row = $row->toArray();

            // Normalize keys (Maatwebsite uses slug: "Start Date" -> "start_date")
            $get = function ($key, $alt = null) use ($row) {
                $v = $row[$key] ?? $row[$alt ?? $key] ?? null;
                return $v !== null && $v !== '' ? trim((string) $v) : null;
            };
            $getRaw = function ($key, $alt = null) use ($row) {
                $v = $row[$key] ?? $row[$alt ?? $key] ?? null;
                return $v;
            };
            $title = $get('title', 'Title');
            $startDateRaw = $getRaw('start_date', 'Start Date');
            $endDateRaw = $getRaw('end_date', 'End Date');
            $startDate = $startDateRaw !== null && $startDateRaw !== '' ? trim((string) $startDateRaw) : null;
            $endDate = $endDateRaw !== null && $endDateRaw !== '' ? trim((string) $endDateRaw) : null;
            if ($title === null && $startDate === null && $endDate === null) {
                continue; // skip empty row
            }
            if ($title === null || $title === '') {
                $errors[] = "Row {$rowNumber}: Title is required.";
                continue;
            }
            if (($startDate === null || $startDate === '') && ($startDateRaw === null || $startDateRaw === '')) {
                $errors[] = "Row {$rowNumber}: Start Date is required.";
                continue;
            }
            if (($endDate === null || $endDate === '') && ($endDateRaw === null || $endDateRaw === '')) {
                $errors[] = "Row {$rowNumber}: End Date is required.";
                continue;
            }
            $startDateParsed = $parseDate($startDateRaw ?? $startDate);
            $endDateParsed = $parseDate($endDateRaw ?? $endDate);
            if ($startDateParsed === null) {
                $errors[] = "Row {$rowNumber}: Invalid Start Date format.";
                continue;
            }
            if ($endDateParsed === null) {
                $errors[] = "Row {$rowNumber}: Invalid End Date format.";
                continue;
            }
            if ($endDateParsed->lt($startDateParsed)) {
                $errors[] = "Row {$rowNumber}: End Date must be on or after Start Date.";
                continue;
            }
            $hoursRaw = $get('hours', 'Hours');
            $hours = $hoursRaw !== null && is_numeric($hoursRaw) ? (int) $hoursRaw : null;
            if ($hours !== null && $hours < 0) {
                $errors[] = "Row {$rowNumber}: Hours must be 0 or greater.";
                continue;
            }
            $attendedDateRaw = $getRaw('attended_date', 'Attended Date');
            $attendedDateParsed = $parseDate($attendedDateRaw);
            $remarks = $get('remarks', 'Remarks');
            $remarks = $remarks !== null ? substr($remarks, 0, 255) : null;

            try {
                DB::transaction(function () use ($user, $row, $get, $hours, $attendedDateParsed, $remarks, $startDateParsed, $endDateParsed, &$imported, &$duplicates) {
                    $title = $get('title', 'Title');
                    $titleNormalized = $title !== null && $title !== '' ? trim(preg_replace('/\s+/', ' ', $title)) : '';

                    // Check for existing training with same dates
                    $candidates = Training::where('start_date', $startDateParsed->format('Y-m-d'))
                        ->where('end_date', $endDateParsed->format('Y-m-d'))
                        ->get();

                    $training = $candidates->first(function ($t) use ($titleNormalized) {
                        $existingNormalized = trim(preg_replace('/\s+/', ' ', (string) $t->title));
                        return $existingNormalized === $titleNormalized;
                    });

                    // Create new training if not found
                    if (! $training) {
                        $training = Training::create([
                            'title' => $titleNormalized ?: $title,
                            'type_of_ld' => $get('type_of_ld', 'Type of L&D') ?: null,
                            'type_of_ld_specify' => $get('type_of_ld_specify', 'Type of L&D (specify)') ?: null,
                            'provider' => $get('provider', 'Provider') ?: null,
                            'venue' => $get('venue', 'Venue') ?: null,
                            'start_date' => $startDateParsed,
                            'end_date' => $endDateParsed,
                            'hours' => $hours,
                            'description' => null,
                            'certificate_number' => null,
                        ]);
                    }

                    $pivot = [
                        'attended_date' => $attendedDateParsed,
                        'remarks' => $remarks,
                    ];

                    // Check if user is already attached to this training
                    $alreadyAttached = $training->users()->where('user_id', $user->id)->exists();
                    $training->users()->syncWithoutDetaching([
                        $user->id => $pivot,
                    ]);

                    if (! $alreadyAttached) {
                        $imported++;
                    } else {
                        $duplicates++;
                    }
                });
            } catch (\Throwable $e) {
                $errors[] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }

        if (! empty($errors)) {
            return response()->json([
                'message' => 'Import completed with some errors.',
                'imported' => $imported,
                'duplicates' => $duplicates,
                'errors' => $errors,
            ], $imported > 0 ? 200 : 422);
        }

        if ($imported === 0 && $duplicates > 0) {
            $message = "{$duplicates} duplicate row(s) were skipped because the trainings were already linked to your record. No new trainings were added.";
        } else {
            $message = "Successfully imported {$imported} training(s) to your record.";
            if ($duplicates > 0) {
                $message .= " {$duplicates} duplicate row(s) were skipped because the trainings were already linked to your record.";
            }
        }

        return response()->json([
            'message' => $message,
            'imported' => $imported,
            'skipped_duplicates' => $duplicates,
            'user_name' => $user->name,
        ], 200);
    }
}

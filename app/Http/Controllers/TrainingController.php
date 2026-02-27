<?php

namespace App\Http\Controllers;

use App\Imports\TrainingsImport;
use App\Models\Training;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class TrainingController extends Controller
{
    /** Display date format (e.g. 09 Feb 2026). */
    private const DATE_DISPLAY = 'd M Y';

    /** Allowed sort columns. */
    private const SORTABLE_COLUMNS = ['start_date', 'title', 'hours'];

    /** Allowed Type of L&D filter values. */
    private const TYPE_OF_LD_VALUES = ['Managerial', 'Supervisory', 'Technical', 'Other'];

    private function formatDateDisplay($value): ?string
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
     * Admin page: manage trainings (list, add, assign personnel).
     */
    public function manage(): \Illuminate\View\View
    {
        return view('trainings.index');
    }

    /**
     * List all trainings (JSON for AJAX) with search, filters, sort, pagination.
     * Query params: q, type, year, min_hours, max_hours, sort, direction, page, per_page.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Training::query();

        if ($request->filled('q')) {
            $s = $request->input('q');
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', '%' . $s . '%')
                    ->orWhere('provider', 'like', '%' . $s . '%')
                    ->orWhere('venue', 'like', '%' . $s . '%');
            });
        }

        if ($request->filled('type_of_ld')) {
            $typeOfLd = $request->input('type_of_ld');
            if (in_array($typeOfLd, self::TYPE_OF_LD_VALUES, true)) {
                $query->where('type_of_ld', $typeOfLd);
            }
        }

        if ($request->filled('year')) {
            $year = (int) $request->input('year');
            if ($year >= 1900 && $year <= 2100) {
                $query->whereYear('start_date', $year);
            }
        }

        if ($request->filled('min_hours')) {
            $min = (int) $request->input('min_hours');
            if ($min >= 0) {
                $query->where('hours', '>=', $min);
            }
        }
        if ($request->filled('max_hours')) {
            $max = (int) $request->input('max_hours');
            if ($max >= 0) {
                $query->where('hours', '<=', $max);
            }
        }

        $sort = $request->input('sort', 'start_date');
        if (! in_array($sort, self::SORTABLE_COLUMNS, true)) {
            $sort = 'start_date';
        }
        $direction = strtolower($request->input('direction', 'desc'));
        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }
        $query->orderBy($sort, $direction);

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }
        $perPage = min(max($perPage, 1), 100);
        $paginator = $query->paginate($perPage);

        $items = collect($paginator->items())->map(function ($training) {
            return [
                'id' => $training->id,
                'title' => $training->title,
                'type_of_ld' => $training->type_of_ld,
                'type_of_ld_specify' => $training->type_of_ld_specify,
                'provider' => $training->provider,
                'venue' => $training->venue,
                'start_date' => $training->start_date?->format('Y-m-d'),
                'end_date' => $training->end_date?->format('Y-m-d'),
                'start_date_display' => $this->formatDateDisplay($training->start_date),
                'end_date_display' => $this->formatDateDisplay($training->end_date),
                'hours' => $training->hours,
                'description' => $training->description,
            ];
        });

        return response()->json([
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
    }

    /**
     * Get a single training (for edit form).
     */
    public function show(Training $training): JsonResponse
    {
        return response()->json(['data' => $training]);
    }

    /**
     * Store a new training.
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
        ]);

        $training = Training::create($validated);
        return response()->json(['data' => $training], 201);
    }

    /**
     * Update a training.
     */
    public function update(Request $request, Training $training): JsonResponse
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
        ]);

        $training->update($validated);
        return response()->json(['data' => $training]);
    }

    /**
     * Delete a training.
     */
    public function destroy(Training $training): JsonResponse
    {
        $training->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }

    /**
     * Assign user(s) to a training (attendees).
     */
    public function attachUsers(Request $request, Training $training): JsonResponse
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', Rule::exists('users', 'id')],
            'attended_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $attendedDate = $validated['attended_date'] ?? null;
        $remarks = $validated['remarks'] ?? null;

        foreach ($validated['user_ids'] as $userId) {
            $training->users()->syncWithoutDetaching([
                $userId => [
                    'attended_date' => $attendedDate,
                    'remarks' => $remarks,
                ],
            ]);
        }

        return response()->json(['message' => 'Personnel attached'], 200);
    }

    /**
     * Remove a user from a training.
     */
    public function detachUser(Training $training, User $user): JsonResponse
    {
        $training->users()->detach($user->id);
        return response()->json(['message' => 'Detached'], 200);
    }

    /**
     * Remove selected or all trainings from a user's record (bulk detach).
     * POST body: { "training_ids": [1, 2, 3] } for selected, or "all": true for all.
     */
    public function detachBulk(Request $request, User $user): JsonResponse
    {
        $all = $request->boolean('all');
        $trainingIds = $request->input('training_ids', []);
        if (! is_array($trainingIds)) {
            $trainingIds = [];
        }
        $trainingIds = array_values(array_unique(array_filter(array_map('intval', $trainingIds))));

        if ($all) {
            $removed = $user->trainings()->count();
            $user->trainings()->detach();
            return response()->json([
                'message' => "Removed {$removed} training(s) from {$user->name}'s record.",
                'removed' => $removed,
            ], 200);
        }

        if (empty($trainingIds)) {
            return response()->json([
                'message' => 'Please select at least one training to remove, or use Remove all.',
            ], 422);
        }

        $removed = 0;
        foreach ($trainingIds as $tid) {
            $user->trainings()->detach($tid);
            $removed++;
        }
        return response()->json([
            'message' => "Removed {$removed} training(s) from {$user->name}'s record.",
            'removed' => $removed,
        ], 200);
    }

    /**
     * Import trainings from Excel and assign all rows to the selected user.
     * Excel columns (same as single-user export): Title, Type, Provider, Venue,
     * Start Date, End Date, Hours, Attended Date, Remarks.
     */
    public function import(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'max:10240'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', Rule::exists('users', 'id')],
        ], [
            'file.required' => 'Please select an Excel file to upload.',
            'file.file' => 'The upload must be a file.',
            'file.max' => 'The file may not be larger than 10 MB.',
            'user_ids.required' => 'Please select at least one user to assign the trainings to.',
            'user_ids.min' => 'Please select at least one user.',
            'user_ids.*.exists' => 'One or more selected users do not exist.',
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

        $validated = $validator->validated();
        $userIds = array_values(array_unique($validated['user_ids']));
        $users = User::whereIn('id', $userIds)->get();
        $file = $request->file('file');

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
                DB::transaction(function () use ($users, $row, $get, $hours, $attendedDateParsed, $remarks, $startDateParsed, $endDateParsed, &$imported, &$duplicates) {
                    $title = $get('title', 'Title');
                    $titleNormalized = $title !== null && $title !== '' ? trim(preg_replace('/\s+/', ' ', $title)) : '';

                    $candidates = Training::where('start_date', $startDateParsed->format('Y-m-d'))
                        ->where('end_date', $endDateParsed->format('Y-m-d'))
                        ->get();

                    $training = $candidates->first(function ($t) use ($titleNormalized) {
                        $existingNormalized = trim(preg_replace('/\s+/', ' ', (string) $t->title));
                        return $existingNormalized === $titleNormalized;
                    });

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
                    $anyNew = false;
                    foreach ($users as $user) {
                        $alreadyAttached = $training->users()->where('user_id', $user->id)->exists();
                        $training->users()->syncWithoutDetaching([
                            $user->id => $pivot,
                        ]);
                        if (! $alreadyAttached) {
                            $anyNew = true;
                        }
                    }
                    $anyNew ? $imported++ : $duplicates++;
                });
            } catch (\Throwable $e) {
                $errors[] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }

        if (! empty($errors)) {
            return response()->json([
                'message' => 'Import completed with some errors.',
                'imported' => $imported,
                'errors' => $errors,
            ], $imported > 0 ? 200 : 422);
        }

        $userCount = $users->count();
        $userLabel = $userCount === 1
            ? $users->first()->name
            : $userCount . ' user(s)';

        if ($imported === 0 && $duplicates > 0) {
            $message = "{$duplicates} duplicate row(s) were skipped because the trainings were already linked. No new trainings were added.";
        } else {
            $message = "Successfully imported {$imported} training(s) for {$userLabel}.";
            if ($duplicates > 0) {
                $message .= " {$duplicates} duplicate row(s) were skipped because the trainings were already linked.";
            }
        }

        return response()->json([
            'message' => $message,
            'imported' => $imported,
            'skipped_duplicates' => $duplicates,
            'user_name' => $userCount === 1 ? $users->first()->name : null,
            'user_count' => $userCount,
        ], 200);
    }
}

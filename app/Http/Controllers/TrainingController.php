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
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Admin page: manage trainings (list, add, assign personnel).
     */
    public function manage(): \Illuminate\View\View
    {
        return view('trainings.index');
    }

    /**
     * List all trainings (JSON for AJAX).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Training::query()->orderBy('start_date', 'desc');

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('provider', 'like', "%{$s}%")
                    ->orWhere('type', 'like', "%{$s}%");
            });
        }

        $trainings = $query->get();
        return response()->json(['data' => $trainings]);
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
            'type' => ['nullable', 'string', 'max:50'],
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
            'type' => ['nullable', 'string', 'max:50'],
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
                            'type' => $get('type', 'Type') ?: null,
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
        ], 200);
    }
}

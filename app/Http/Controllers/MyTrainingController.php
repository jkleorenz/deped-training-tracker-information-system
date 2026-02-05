<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyTrainingController extends Controller
{
    /**
     * Format a date value (string, Carbon, or DateTime) to Y-m-d for API. Pivot dates are often raw strings.
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
     * Get the current user's trainings (for dashboard list). Uses auth user so data matches PDF report.
     */
    public function myRecord(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthenticated.', 'data' => []], 401);
            }

            $query = $user->trainings()->withPivot(['attended_date', 'remarks']);

            if ($request->filled('date_from')) {
                $query->where('trainings.start_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->where('trainings.end_date', '<=', $request->input('date_to'));
            }
            if ($request->filled('type')) {
                $query->where('trainings.type', $request->input('type'));
            }

            $trainings = $query->orderBy('trainings.start_date', 'desc')->get();

            $items = $trainings->map(function ($training) {
                return [
                    'id' => $training->id,
                    'title' => $training->title,
                    'type' => $training->type,
                    'provider' => $training->provider,
                    'venue' => $training->venue,
                    'start_date' => $this->formatDateForApi($training->start_date),
                    'end_date' => $this->formatDateForApi($training->end_date),
                    'hours' => $training->hours,
                    'attended_date' => $this->formatDateForApi($training->pivot?->attended_date),
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
                    ->orWhere('type', 'like', "%{$s}%");
            });
        }

        $trainings = $query->get(['id', 'title', 'type', 'provider', 'venue', 'start_date', 'end_date', 'hours']);
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
            'type' => ['nullable', 'string', 'max:50'],
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
}

<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
}

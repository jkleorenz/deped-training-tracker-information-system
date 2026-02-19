<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersonnelController extends Controller
{
    /**
     * Format a date value (string, Carbon, or DateTime) to Y-m-d for API.
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
     * Admin: list all personnel (server-rendered; optional search via query string).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()->where('role', User::ROLE_PERSONNEL)->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('employee_id', 'like', '%' . $search . '%')
                    ->orWhere('designation', 'like', '%' . $search . '%')
                    ->orWhere('school', 'like', '%' . $search . '%');
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }
        $perPage = min(max($perPage, 1), 100);
        $personnel = $query->paginate($perPage)->withQueryString();
        $personnelList = User::where('role', User::ROLE_PERSONNEL)->orderBy('name')->get(['id', 'name']);

        return view('personnel.index', [
            'personnel' => $personnel,
            'personnel_list' => $personnelList,
        ]);
    }

    /**
     * Admin: view a specific personnel — profile, metadata, seminars & trainings attended.
     */
    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load(['trainings' => function ($q) {
            $q->orderBy('trainings.start_date', 'desc');
        }, 'personalDataSheet']);

        return view('personnel.show', [
            'user' => $user,
        ]);
    }

    /**
     * JSON: list personnel for AJAX (admin only).
     */
    public function list(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()->where('role', User::ROLE_PERSONNEL)->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('school', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) $request->input('per_page', 50), 100);
        $personnel = $query->paginate($perPage, ['id', 'name', 'email', 'employee_id', 'designation', 'school']);

        return response()->json(['data' => $personnel->items(), 'meta' => ['total' => $personnel->total(), 'per_page' => $personnel->perPage()]]);
    }

    /**
     * JSON: get trainings attended by a user (admin or own record).
     */
    public function trainings(Request $request, User $user): JsonResponse
    {
        $this->authorize('view', $user);

        $query = $user->trainings()->withPivot(['attended_date', 'remarks']);

        if ($request->filled('date_from')) {
            $query->where('trainings.start_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('trainings.end_date', '<=', $request->input('date_to'));
        }
        if ($request->filled('type_of_ld')) {
            $query->where('trainings.type_of_ld', $request->input('type_of_ld'));
        }

        $trainings = $query->orderBy('trainings.start_date', 'desc')->get();

        $items = $trainings->map(function ($training) {
            return [
                'id' => $training->id,
                'title' => $training->title,
                'type_of_ld' => $training->type_of_ld,
                'type_of_ld_specify' => $training->type_of_ld_specify,
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
            'data' => $items,
        ]);
    }
}

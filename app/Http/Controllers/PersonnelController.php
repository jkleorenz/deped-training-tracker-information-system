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

        $authUser = auth()->user();
        $isAdmin = $authUser->isAdmin();

        // Base query: admin sees all, sub-admin sees only personnel
        $query = User::query();
        if ($isAdmin) {
            $query->whereIn('role', [User::ROLE_PERSONNEL, User::ROLE_SUB_ADMIN]);
        } else {
            $query->where('role', User::ROLE_PERSONNEL);
        }

        // Apply filters
        $query = $this->applyFilters($query, $request);

        $query->orderBy('name');

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }
        $perPage = min(max($perPage, 1), 100);
        $personnel = $query->paginate($perPage)->withQueryString();

        // Get distinct values for filter dropdowns
        $schools = User::where('role', User::ROLE_PERSONNEL)
            ->whereNotNull('school')
            ->distinct()
            ->pluck('school')
            ->sort()
            ->values();
        $designations = User::where('role', User::ROLE_PERSONNEL)
            ->whereNotNull('designation')
            ->distinct()
            ->pluck('designation')
            ->sort()
            ->values();

        // For export modal: get all personnel
        $personnelList = User::where('role', User::ROLE_PERSONNEL)->orderBy('name')->get(['id', 'name']);

        return view('personnel.index', [
            'personnel' => $personnel,
            'personnel_list' => $personnelList,
            'schools' => $schools,
            'designations' => $designations,
            'isAdmin' => $isAdmin,
        ]);
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters($query, Request $request)
    {
        // Search filter
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



        // Role filter (admin only)
        if ($request->filled('role')) {
            $role = $request->input('role');
            $authUser = auth()->user();
            if ($authUser->isAdmin() && in_array($role, [User::ROLE_PERSONNEL, User::ROLE_SUB_ADMIN], true)) {
                $query->where('role', $role);
            }
        }

        // School filter
        if ($request->filled('school')) {
            $query->where('school', $request->input('school'));
        }

        // Designation filter
        if ($request->filled('designation')) {
            $query->where('designation', $request->input('designation'));
        }

        return $query;
    }

    /**
     * Admin: view a specific personnel — profile, metadata, seminars & trainings attended.
     */
    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->loadCount('trainings');
        $user->load('personalDataSheet');

        return view('personnel.show', [
            'user' => $user,
        ]);
    }

    /**
     * JSON: list personnel for AJAX (admin and sub-admin).
     */
    public function list(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $authUser = auth()->user();
        $isAdmin = $authUser->isAdmin();

        // Base query: admin sees all, sub-admin sees only personnel
        $query = User::query();
        if ($isAdmin) {
            $query->whereIn('role', [User::ROLE_PERSONNEL, User::ROLE_SUB_ADMIN]);
        } else {
            $query->where('role', User::ROLE_PERSONNEL);
        }

        // Apply filters
        $query = $this->applyFilters($query, $request);

        $query->orderBy('name');

        $perPage = min((int) $request->input('per_page', 50), 100);
        $personnel = $query->paginate($perPage, ['id', 'name', 'email', 'employee_id', 'designation', 'school', 'role', 'status']);

        return response()->json([
            'data' => $personnel->items(),
            'meta' => [
                'total' => $personnel->total(),
                'per_page' => $personnel->perPage(),
                'current_page' => $personnel->currentPage(),
                'last_page' => $personnel->lastPage(),
            ],
        ]);
    }

    /**
     * JSON: get trainings attended by a user (admin or own record), paginated.
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

        $perPage = min((int) $request->input('per_page', 10), 100);
        $perPage = $perPage >= 1 ? $perPage : 10;
        $paginator = $query->orderBy('trainings.start_date', 'desc')->paginate($perPage);

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
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }
}

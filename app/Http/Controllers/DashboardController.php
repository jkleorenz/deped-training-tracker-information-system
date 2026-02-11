<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            // Single query for counts — no full table loads, no extra API calls on page load
            $personnelCount = User::where('role', User::ROLE_PERSONNEL)->count();
            return view('dashboard.admin', [
                'user' => $user,
                'trainings_count' => Training::count(),
                'personnel_count' => $personnelCount,
                'personnel_list' => User::where('role', User::ROLE_PERSONNEL)->orderBy('name')->get(['id', 'name']),
            ]);
        }

        // Personnel: eager-load trainings once for the table (no N+1)
        $user->load([
            'trainings' => fn ($q) => $q->orderBy('trainings.start_date', 'desc')->withPivot(['attended_date', 'remarks']),
        ]);

        return view('dashboard.personnel', [
            'user' => $user,
            'trainings' => $user->trainings,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return view('dashboard.admin');
        }

        $trainings = $user->trainings()
            ->withPivot(['attended_date', 'remarks'])
            ->orderBy('trainings.start_date', 'desc')
            ->get();

        return view('dashboard.personnel', [
            'user' => $user,
            'trainings' => $trainings,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\TrainingsExport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Export seminars/trainings to Excel.
     * Admin: can export for a specific user or all.
     * Personnel: own records only.
     */
    public function excel(Request $request): BinaryFileResponse
    {
        $user = Auth::user();
        $userId = $request->input('user_id');

        if ($user->isAdmin() && $userId) {
            $targetUser = User::findOrFail($userId);
            $this->authorize('view', $targetUser);
            $trainings = $targetUser->trainings()->orderBy('trainings.start_date', 'desc')->get();
            $exportUser = $targetUser;
        } elseif ($user->isAdmin() && ! $userId) {
            $trainings = \App\Models\Training::with('users')->orderBy('start_date', 'desc')->get();
            return Excel::download(new TrainingsExport($trainings, null), 'deped_trainings_all.xlsx');
        } else {
            $exportUser = $user;
            $trainings = $user->trainings()->orderBy('trainings.start_date', 'desc')->get();
        }

        return Excel::download(
            new TrainingsExport($trainings, $exportUser),
            'deped_trainings_' . preg_replace('/[^a-z0-9]/', '_', strtolower($exportUser->name)) . '.xlsx'
        );
    }

    /**
     * Printable PDF report for a user's trainings.
     */
    public function pdf(Request $request): Response
    {
        $user = Auth::user();
        $userId = $request->input('user_id');

        if ($user->isAdmin() && $userId) {
            $targetUser = User::findOrFail($userId);
            $this->authorize('view', $targetUser);
            $reportUser = $targetUser;
        } else {
            $reportUser = $user;
        }

        $trainings = $reportUser->trainings()->orderBy('trainings.start_date', 'desc')->get();

        $pdf = Pdf::loadView('reports.trainings-pdf', [
            'user' => $reportUser,
            'trainings' => $trainings,
            'appName' => config('app.name'),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('training_report_' . $reportUser->id . '.pdf');
    }
}

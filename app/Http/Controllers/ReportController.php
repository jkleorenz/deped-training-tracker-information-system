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
     * Admin/Sub-admin: can export for specific user(s) via user_id or user_id[], or all.
     * Personnel: own records only.
     */
    public function excel(Request $request): BinaryFileResponse
    {
        $user = Auth::user();
        $userIds = $request->input('user_id');
        $userIds = is_array($userIds) ? $userIds : ($userIds !== null && $userIds !== '' ? [$userIds] : []);

        if ($user->isAdminOrSubAdmin() && $userIds !== []) {
            $targetUsers = User::whereIn('id', $userIds)->where('role', User::ROLE_PERSONNEL)->get();
            foreach ($targetUsers as $targetUser) {
                $this->authorize('view', $targetUser);
            }
            if ($targetUsers->count() === 1) {
                $exportUser = $targetUsers->first();
                $trainings = $exportUser->trainings()->orderBy('trainings.start_date', 'desc')->get();
                return Excel::download(
                    new TrainingsExport($trainings, $exportUser),
                    'deped_trainings_' . preg_replace('/[^a-z0-9]/', '_', strtolower($exportUser->name)) . '.xlsx'
                );
            }
            if ($targetUsers->count() > 1) {
                $filename = 'deped_trainings_selected_' . date('Y-m-d_His') . '.xlsx';
                return Excel::download(
                    new TrainingsExport(collect(), null, $targetUsers->all()),
                    $filename
                );
            }
        }

        if ($user->isAdminOrSubAdmin() && $userIds === []) {
            $trainings = \App\Models\Training::with('users')->orderBy('start_date', 'desc')->get();
            return Excel::download(new TrainingsExport($trainings, null), 'deped_trainings_all.xlsx');
        }

        $exportUser = $user;
        $trainings = $user->trainings()->orderBy('trainings.start_date', 'desc')->get();

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

        if ($user->isAdminOrSubAdmin() && $userId) {
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

        return $pdf->stream('training_report_' . $reportUser->id . '.pdf', ['Attachment' => false]);
    }

    /**
     * Personal Data Sheet (CS Form No. 212) as PDF.
     */
    public function pdsPdf(Request $request): Response
    {
        $authUser = Auth::user();
        $userId = $request->input('user_id');

        if ($authUser->isAdminOrSubAdmin() && $userId) {
            $targetUser = User::findOrFail($userId);
            $this->authorize('view', $targetUser);
            $reportUser = $targetUser;
        } else {
            $reportUser = $authUser;
        }

        $reportUser->load(['personalDataSheet.civilServiceEligibilities', 'personalDataSheet.workExperiences']);
        $pds = $reportUser->personalDataSheet;

        $pdf = Pdf::loadView('reports.pds-pdf', [
            'user' => $reportUser,
            'pds' => $pds,
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('footer-right', 'CS FORM 212 (Revised 2025), Page [page] of [topage]');
        $pdf->setOption('footer-font-size', 7);

        return $pdf->stream('personal_data_sheet_' . $reportUser->id . '.pdf', ['Attachment' => false]);
    }
}

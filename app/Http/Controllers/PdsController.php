<?php

namespace App\Http\Controllers;

use App\Models\PersonalDataSheet;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PdsController extends Controller
{
    /**
     * Show PDS form for the given user (or current user if no user specified).
     * Personnel: own only. Admin/Sub-admin: can open any user's PDS.
     */
    public function edit(Request $request, ?User $user = null): View|RedirectResponse
    {
        $targetUser = $user ?? $request->user();
        if ($targetUser === null) {
            $targetUser = $request->user();
        }
        $this->authorize('view', $targetUser);

        $pds = $targetUser->personalDataSheet ?? new PersonalDataSheet(['user_id' => $targetUser->id]);
        $isOwn = $request->user()->id === $targetUser->id;

        return view('pds.edit', [
            'user' => $targetUser,
            'pds' => $pds,
            'isOwn' => $isOwn,
        ]);
    }

    /**
     * Save or update PDS for the given user.
     */
    public function update(Request $request, ?User $user = null): RedirectResponse
    {
        $targetUser = $user ?? $request->user();
        $this->authorize('update', $targetUser);

        $pds = $targetUser->personalDataSheet ?? new PersonalDataSheet(['user_id' => $targetUser->id]);

        $validated = $request->validate([
            'surname' => ['nullable', 'string', 'max:100'],
            'name_extension' => ['nullable', 'string', 'max:20'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'sex' => ['nullable', Rule::in(['male', 'female'])],
            'civil_status' => ['nullable', Rule::in(['single', 'married', 'widowed', 'separated', 'other'])],
            'civil_status_other' => ['nullable', 'string', 'max:50'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:3'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'umid_id' => ['nullable', 'string', 'max:50'],
            'pagibig_id' => ['nullable', 'string', 'max:50'],
            'philhealth_no' => ['nullable', 'string', 'max:50'],
            'philsys_number' => ['nullable', 'string', 'max:50'],
            'tin_no' => ['nullable', 'string', 'max:50'],
            'agency_employee_no' => ['nullable', 'string', 'max:50'],
            'date_of_appointment' => ['nullable', 'date'],
            'citizenship' => ['nullable', Rule::in(['filipino', 'dual'])],
            'dual_citizenship_type' => ['nullable', Rule::in(['by_birth', 'by_naturalization'])],
            'dual_citizenship_country' => ['nullable', 'string', 'max:100'],
            'residential_house_no' => ['nullable', 'string', 'max:100'],
            'residential_street' => ['nullable', 'string', 'max:255'],
            'residential_subdivision' => ['nullable', 'string', 'max:255'],
            'residential_barangay' => ['nullable', 'string', 'max:100'],
            'residential_city' => ['nullable', 'string', 'max:100'],
            'residential_province' => ['nullable', 'string', 'max:100'],
            'residential_zip' => ['nullable', 'string', 'max:20'],
            'permanent_house_no' => ['nullable', 'string', 'max:100'],
            'permanent_street' => ['nullable', 'string', 'max:255'],
            'permanent_subdivision' => ['nullable', 'string', 'max:255'],
            'permanent_barangay' => ['nullable', 'string', 'max:100'],
            'permanent_city' => ['nullable', 'string', 'max:100'],
            'permanent_province' => ['nullable', 'string', 'max:100'],
            'permanent_zip' => ['nullable', 'string', 'max:20'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'email_address' => ['nullable', 'email', 'max:255'],
        ]);

        $pds->fill($validated);
        $pds->user_id = $targetUser->id;
        $pds->save();

        $message = $pds->wasRecentlyCreated ? 'Personal Data Sheet created.' : 'Personal Data Sheet updated.';
        $route = $targetUser->id === $request->user()->id
            ? route('pds.edit')
            : route('personnel.pds.edit', $targetUser);

        return redirect($route)->with('success', $message);
    }
}

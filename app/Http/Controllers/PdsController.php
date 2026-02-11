<?php

namespace App\Http\Controllers;

use App\Models\PersonalDataSheet;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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

        $validated = $request->validate($this->pdsValidationRules($request, false));

        $pds->fill($validated);
        $pds->user_id = $targetUser->id;
        $pds->save();

        // Sync Section IV. Civil Service Eligibility (up to 5 rows)
        $pds->civilServiceEligibilities()->delete();
        foreach ($request->input('eligibility', []) as $i => $row) {
            $row = array_filter($row);
            if (! empty($row)) {
                $pds->civilServiceEligibilities()->create([
                    'eligibility_type' => $row['eligibility_type'] ?? null,
                    'rating' => $row['rating'] ?? null,
                    'date_exam_conferment' => isset($row['date_exam_conferment']) && $row['date_exam_conferment'] ? $row['date_exam_conferment'] : null,
                    'place_exam_conferment' => $row['place_exam_conferment'] ?? null,
                    'license_number' => $row['license_number'] ?? null,
                    'license_valid_until' => isset($row['license_valid_until']) && $row['license_valid_until'] ? $row['license_valid_until'] : null,
                    'sort_order' => $i,
                ]);
            }
        }

        // Sync Section V. Work Experience
        $pds->workExperiences()->delete();
        foreach ($request->input('work', []) as $i => $row) {
            $row = array_filter($row);
            if (! empty($row)) {
                $pds->workExperiences()->create([
                    'from_date' => isset($row['from_date']) && $row['from_date'] ? $row['from_date'] : null,
                    'to_date' => isset($row['to_date']) && $row['to_date'] ? $row['to_date'] : null,
                    'position_title' => $row['position_title'] ?? null,
                    'department_agency' => $row['department_agency'] ?? null,
                    'status_of_appointment' => $row['status_of_appointment'] ?? null,
                    'govt_service_yn' => $row['govt_service_yn'] ?? null,
                    'sort_order' => $i,
                ]);
            }
        }

        $message = $pds->wasRecentlyCreated ? 'Personal Data Sheet created.' : 'Personal Data Sheet updated.';
        $route = $targetUser->id === $request->user()->id
            ? route('pds.edit')
            : route('personnel.pds.edit', $targetUser);

        return redirect($route)->with('success', $message);
    }

    /**
     * Validation rules shared by update and storeDraft (draft allows all nullable).
     */
    protected function pdsValidationRules(Request $request, bool $strict = false): array
    {
        $rules = [
            'surname' => [$strict ? 'required' : 'nullable', 'string', 'max:100'],
            'name_extension' => ['nullable', 'string', 'max:20'],
            'first_name' => [$strict ? 'required' : 'nullable', 'string', 'max:100'],
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
            'spouse_surname' => ['nullable', 'string', 'max:100'],
            'spouse_first_name' => ['nullable', 'string', 'max:100'],
            'spouse_middle_name' => ['nullable', 'string', 'max:100'],
            'spouse_occupation' => ['nullable', 'string', 'max:255'],
            'spouse_employer_business_name' => ['nullable', 'string', 'max:255'],
            'spouse_business_address' => ['nullable', 'string', 'max:500'],
            'spouse_telephone' => ['nullable', 'string', 'max:50'],
            'children_names' => ['nullable', 'string', 'max:2000'],
            'father_surname' => ['nullable', 'string', 'max:100'],
            'father_first_name' => ['nullable', 'string', 'max:100'],
            'father_middle_name' => ['nullable', 'string', 'max:100'],
            'mother_surname' => ['nullable', 'string', 'max:100'],
            'mother_first_name' => ['nullable', 'string', 'max:100'],
            'mother_middle_name' => ['nullable', 'string', 'max:100'],
            'elem_school' => ['nullable', 'string', 'max:255'],
            'elem_degree_course' => ['nullable', 'string', 'max:255'],
            'elem_period_from' => ['nullable', 'string', 'max:20'],
            'elem_period_to' => ['nullable', 'string', 'max:20'],
            'elem_highest_level_units' => ['nullable', 'string', 'max:255'],
            'elem_scholarship_honors' => ['nullable', 'string', 'max:255'],
            'secondary_school' => ['nullable', 'string', 'max:255'],
            'secondary_degree_course' => ['nullable', 'string', 'max:255'],
            'secondary_period_from' => ['nullable', 'string', 'max:20'],
            'secondary_period_to' => ['nullable', 'string', 'max:20'],
            'secondary_highest_level_units' => ['nullable', 'string', 'max:255'],
            'secondary_scholarship_honors' => ['nullable', 'string', 'max:255'],
            'voc_school' => ['nullable', 'string', 'max:255'],
            'voc_degree_course' => ['nullable', 'string', 'max:255'],
            'voc_period_from' => ['nullable', 'string', 'max:20'],
            'voc_period_to' => ['nullable', 'string', 'max:20'],
            'voc_highest_level_units' => ['nullable', 'string', 'max:255'],
            'voc_scholarship_honors' => ['nullable', 'string', 'max:255'],
            'college_school' => ['nullable', 'string', 'max:255'],
            'college_degree_course' => ['nullable', 'string', 'max:255'],
            'college_period_from' => ['nullable', 'string', 'max:20'],
            'college_period_to' => ['nullable', 'string', 'max:20'],
            'college_highest_level_units' => ['nullable', 'string', 'max:255'],
            'college_scholarship_honors' => ['nullable', 'string', 'max:255'],
            'grad_school' => ['nullable', 'string', 'max:255'],
            'grad_degree_course' => ['nullable', 'string', 'max:255'],
            'grad_period_from' => ['nullable', 'string', 'max:20'],
            'grad_period_to' => ['nullable', 'string', 'max:20'],
            'grad_highest_level_units' => ['nullable', 'string', 'max:255'],
            'grad_scholarship_honors' => ['nullable', 'string', 'max:255'],
            'eligibility' => ['nullable', 'array'],
            'eligibility.*.eligibility_type' => ['nullable', 'string', 'max:500'],
            'eligibility.*.rating' => ['nullable', 'string', 'max:50'],
            'eligibility.*.date_exam_conferment' => ['nullable', 'date'],
            'eligibility.*.place_exam_conferment' => ['nullable', 'string', 'max:255'],
            'eligibility.*.license_number' => ['nullable', 'string', 'max:100'],
            'eligibility.*.license_valid_until' => ['nullable', 'date'],
            'work' => ['nullable', 'array'],
            'work.*.from_date' => ['nullable', 'date'],
            'work.*.to_date' => [
                'nullable',
                'date',
                function (string $attribute, $value, \Closure $fail) use ($request): void {
                    if (empty($value)) {
                        return;
                    }
                    if (! preg_match('/^work\.(\d+)\.to_date$/', $attribute, $m)) {
                        return;
                    }
                    $index = $m[1];
                    $from = $request->input("work.{$index}.from_date");
                    if (! empty($from) && \Carbon\Carbon::parse($value)->lt(\Carbon\Carbon::parse($from))) {
                        $fail('The end date must be on or after the start date.');
                    }
                },
            ],
            'work.*.position_title' => ['nullable', 'string', 'max:255'],
            'work.*.department_agency' => ['nullable', 'string', 'max:500'],
            'work.*.status_of_appointment' => ['nullable', 'string', 'max:100'],
            'work.*.govt_service_yn' => ['nullable', Rule::in(['Y', 'N'])],
        ];
        return $rules;
    }

    /**
     * Save draft (autosave / Save Draft button). Accepts partial data, returns JSON.
     */
    public function storeDraft(Request $request, ?User $user = null): JsonResponse
    {
        $targetUser = $user ?? $request->user();
        $this->authorize('update', $targetUser);

        $validated = $request->validate($this->pdsValidationRules($request, false));

        $pds = $targetUser->personalDataSheet ?? new PersonalDataSheet(['user_id' => $targetUser->id]);
        $pds->fill($validated);
        $pds->user_id = $targetUser->id;
        $pds->save();

        $pds->civilServiceEligibilities()->delete();
        foreach ($request->input('eligibility', []) as $i => $row) {
            $row = array_filter($row);
            if (! empty($row)) {
                $pds->civilServiceEligibilities()->create([
                    'eligibility_type' => $row['eligibility_type'] ?? null,
                    'rating' => $row['rating'] ?? null,
                    'date_exam_conferment' => isset($row['date_exam_conferment']) && $row['date_exam_conferment'] ? $row['date_exam_conferment'] : null,
                    'place_exam_conferment' => $row['place_exam_conferment'] ?? null,
                    'license_number' => $row['license_number'] ?? null,
                    'license_valid_until' => isset($row['license_valid_until']) && $row['license_valid_until'] ? $row['license_valid_until'] : null,
                    'sort_order' => $i,
                ]);
            }
        }

        $pds->workExperiences()->delete();
        foreach ($request->input('work', []) as $i => $row) {
            $row = array_filter($row);
            if (! empty($row)) {
                $pds->workExperiences()->create([
                    'from_date' => isset($row['from_date']) && $row['from_date'] ? $row['from_date'] : null,
                    'to_date' => isset($row['to_date']) && $row['to_date'] ? $row['to_date'] : null,
                    'position_title' => $row['position_title'] ?? null,
                    'department_agency' => $row['department_agency'] ?? null,
                    'status_of_appointment' => $row['status_of_appointment'] ?? null,
                    'govt_service_yn' => $row['govt_service_yn'] ?? null,
                    'sort_order' => $i,
                ]);
            }
        }

        return response()->json(['saved' => true, 'message' => 'Draft saved.']);
    }
}

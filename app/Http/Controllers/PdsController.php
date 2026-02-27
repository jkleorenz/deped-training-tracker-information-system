<?php

namespace App\Http\Controllers;

use App\Models\CivilServiceEligibility;
use App\Models\PersonalDataSheet;
use App\Models\User;
use App\Models\WorkExperience;
use App\Services\PdsPhotoService;
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

        $validated = $request->validate([
            'surname' => ['required', 'string', 'max:100'],
            'name_extension' => ['nullable', 'string', 'max:20'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'sex' => ['nullable', Rule::in(['male', 'female'])],
            'civil_status' => ['nullable', Rule::in(['single', 'married', 'widowed', 'separated', 'other', ''])],
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
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            // Section II. Family Background
            'spouse_surname' => ['nullable', 'string', 'max:100'],
            'spouse_name_extension' => ['nullable', 'string', 'max:20'],
            'spouse_first_name' => ['nullable', 'string', 'max:100'],
            'spouse_middle_name' => ['nullable', 'string', 'max:100'],
            'spouse_occupation' => ['nullable', 'string', 'max:255'],
            'spouse_employer_business_name' => ['nullable', 'string', 'max:255'],
            'spouse_business_address' => ['nullable', 'string', 'max:500'],
            'spouse_telephone' => ['nullable', 'string', 'max:50'],
            'children_names' => ['nullable', 'string'],
            'children_data' => ['nullable', 'string'],
            'father_surname' => ['nullable', 'string', 'max:100'],
            'father_first_name' => ['nullable', 'string', 'max:100'],
            'father_middle_name' => ['nullable', 'string', 'max:100'],
            'father_name_extension' => ['nullable', 'string', 'max:20'],
            'mother_surname' => ['nullable', 'string', 'max:100'],
            'mother_first_name' => ['nullable', 'string', 'max:100'],
            'mother_middle_name' => ['nullable', 'string', 'max:100'],
            'mother_name_extension' => ['nullable', 'string', 'max:20'],
            // Section III. Educational Background (elem, secondary, voc, college, grad)
            ...collect(['elem', 'secondary', 'voc', 'college', 'grad'])->flatMap(function ($prefix) {
                return [
                    "{$prefix}_school" => ['nullable', 'string', 'max:255'],
                    "{$prefix}_degree_course" => ['nullable', 'string', 'max:255'],
                    "{$prefix}_period_from" => ['nullable', 'string', 'max:20'],
                    "{$prefix}_period_to" => ['nullable', 'string', 'max:20'],
                    "{$prefix}_highest_level_units" => ['nullable', 'string', 'max:255'],
                    "{$prefix}_year_graduated" => ['nullable', 'string', 'max:10'],
                    "{$prefix}_scholarship_honors" => ['nullable', 'string', 'max:255'],
                ];
            })->all(),
            // Section IV. Civil Service Eligibility (repeatable)
            'eligibility' => ['nullable', 'array'],
            'eligibility.*.eligibility_type' => ['nullable', 'string', 'max:500'],
            'eligibility.*.rating' => ['nullable', 'string', 'max:50'],
            'eligibility.*.date_exam_conferment' => ['nullable', 'date'],
            'eligibility.*.license_valid_until' => ['nullable', 'date'],
            'eligibility.*.place_exam_conferment' => ['nullable', 'string', 'max:255'],
            'eligibility.*.license_number' => ['nullable', 'string', 'max:100'],
            // Section V. Work Experience (repeatable)
            'work' => ['nullable', 'array'],
            'work.*.from_date' => ['nullable', 'date'],
            'work.*.to_date' => ['nullable', 'date'],
            'work.*.position_title' => ['nullable', 'string', 'max:255'],
            'work.*.department_agency' => ['nullable', 'string', 'max:500'],
            'work.*.status_of_appointment' => ['nullable', 'string', 'max:100'],
            'work.*.govt_service_yn' => ['nullable', Rule::in(['', 'Y', 'N'])],
            // Section VI. Voluntary Work (repeatable)
            'voluntary' => ['nullable', 'array'],
            'voluntary.*.conducted_sponsored_by' => ['nullable', 'string', 'max:500'],
            'voluntary.*.inclusive_dates_from' => ['nullable', 'date'],
            'voluntary.*.inclusive_dates_to' => ['nullable', 'date'],
            'voluntary.*.position_nature_of_work' => ['nullable', 'string', 'max:255'],
            'voluntary.*.number_of_hours' => ['nullable', 'numeric', 'min:0'],
            // Section VII. Learning and Development (repeatable)
            'learning_development' => ['nullable', 'array'],
            'learning_development.*.organization_name_address' => ['nullable', 'string', 'max:500'],
            'learning_development.*.title_of_ld' => ['nullable', 'string', 'max:500'],
            'learning_development.*.type_of_ld' => ['nullable', 'string', 'max:100'],
            'learning_development.*.type_of_ld_specify' => ['nullable', 'string', 'max:100'],
            'learning_development.*.number_of_hours' => ['nullable', 'numeric', 'min:0'],
            'learning_development.*.inclusive_dates_from' => ['nullable', 'date'],
            'learning_development.*.inclusive_dates_to' => ['nullable', 'date'],
            // Section VIII. Other Information
            'special_skills_hobbies' => ['nullable', 'string', 'max:1000'],
            'non_academic_distinctions' => ['nullable', 'string', 'max:1000'],
            'membership_in_associations' => ['nullable', 'string', 'max:1000'],
            // Page 4 – Questions 34–40 (Y/N + details)
            'admin_offense_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'admin_offense_details' => ['nullable', 'string', 'max:1000'],
            'related_third_degree_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'related_fourth_degree_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'related_authority_details' => ['nullable', 'string', 'max:1000'],
            'indigenous_group_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'indigenous_group_specify' => ['nullable', 'string', 'max:1000'],
            'pwd_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'pwd_id_no' => ['nullable', 'string', 'max:255'],
            'solo_parent_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'solo_parent_id_no' => ['nullable', 'string', 'max:255'],
            'separated_from_service_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'separated_from_service_details' => ['nullable', 'string', 'max:1000'],
            'immigrant_resident_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'immigrant_resident_details' => ['nullable', 'string', 'max:255'],
            'candidate_election_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'candidate_election_details' => ['nullable', 'string', 'max:1000'],
            'resigned_campaign_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'resigned_campaign_details' => ['nullable', 'string', 'max:1000'],
            'criminally_charged_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'criminally_charged_date_filed' => ['nullable', 'string', 'max:100'],
            'criminally_charged_status' => ['nullable', 'string', 'max:255'],
            'criminally_charged_details' => ['nullable', 'string', 'max:1000'],
            'convicted_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'convicted_details' => ['nullable', 'string', 'max:1000'],
            // 41. References
            'ref1_name' => ['nullable', 'string', 'max:255'],
            'ref1_address' => ['nullable', 'string', 'max:1000'],
            'ref1_contact' => ['nullable', 'string', 'max:255'],
            'ref2_name' => ['nullable', 'string', 'max:255'],
            'ref2_address' => ['nullable', 'string', 'max:1000'],
            'ref2_contact' => ['nullable', 'string', 'max:255'],
            'ref3_name' => ['nullable', 'string', 'max:255'],
            'ref3_address' => ['nullable', 'string', 'max:1000'],
            'ref3_contact' => ['nullable', 'string', 'max:255'],
            // 42. Declaration & Government ID
            'govt_id_type' => ['nullable', 'string', 'max:100'],
            'govt_id_number' => ['nullable', 'string', 'max:100'],
            'govt_id_place_date_issue' => ['nullable', 'string', 'max:255'],
            'date_accomplished' => ['nullable', 'date'],
        ]);

        $childrenData = null;
        if ($request->filled('children_data')) {
            $decoded = json_decode($request->children_data, true);
            $childrenData = is_array($decoded) ? $decoded : null;
        }
        unset($validated['children_data'], $validated['eligibility'], $validated['work'], $validated['voluntary'], $validated['learning_development']);
        if (($validated['civil_status'] ?? '') === 'single') {
            foreach (['spouse_surname', 'spouse_name_extension', 'spouse_first_name', 'spouse_middle_name', 'spouse_occupation', 'spouse_employer_business_name', 'spouse_business_address', 'spouse_telephone'] as $key) {
                $validated[$key] = null;
            }
        }
        $pds->fill($validated);
        $pds->user_id = $targetUser->id;
        if ($childrenData !== null) {
            $pds->children_data = $childrenData;
        }
        $pds->save();

        $this->syncCivilServiceEligibilities($pds, $request->input('eligibility', []));
        $this->syncWorkExperiences($pds, $request->input('work', []));
        $this->syncVoluntaryWorks($pds, $request->input('voluntary', []));
        $this->syncLearningDevelopments($pds, $request->input('learning_development', []));

        if ($request->hasFile('photo')) {
            $photoService = app(PdsPhotoService::class);
            $photoService->delete($pds->photo_path);
            $stored = $photoService->processAndStore($request->file('photo'), $pds->id);
            if ($stored !== null) {
                $pds->photo_path = $stored;
                $pds->save();
            }
        }

        $message = $pds->wasRecentlyCreated ? 'Personal Data Sheet created.' : 'Personal Data Sheet updated.';
        $route = $targetUser->id === $request->user()->id
            ? route('pds.edit')
            : route('personnel.pds.edit', $targetUser);

        return redirect($route)->with('success', $message);
    }

    /**
     * Save PDS draft (AJAX). Same as update but returns JSON for own or personnel context.
     */
    public function draft(Request $request, ?User $user = null): JsonResponse
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
            'civil_status' => ['nullable', Rule::in(['single', 'married', 'widowed', 'separated', 'other', ''])],
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
            // Section II. Family Background
            'spouse_surname' => ['nullable', 'string', 'max:100'],
            'spouse_name_extension' => ['nullable', 'string', 'max:20'],
            'spouse_first_name' => ['nullable', 'string', 'max:100'],
            'spouse_middle_name' => ['nullable', 'string', 'max:100'],
            'spouse_occupation' => ['nullable', 'string', 'max:255'],
            'spouse_employer_business_name' => ['nullable', 'string', 'max:255'],
            'spouse_business_address' => ['nullable', 'string', 'max:500'],
            'spouse_telephone' => ['nullable', 'string', 'max:50'],
            'children_names' => ['nullable', 'string'],
            'children_data' => ['nullable', 'string'],
            'father_surname' => ['nullable', 'string', 'max:100'],
            'father_first_name' => ['nullable', 'string', 'max:100'],
            'father_middle_name' => ['nullable', 'string', 'max:100'],
            'father_name_extension' => ['nullable', 'string', 'max:20'],
            'mother_surname' => ['nullable', 'string', 'max:100'],
            'mother_first_name' => ['nullable', 'string', 'max:100'],
            'mother_middle_name' => ['nullable', 'string', 'max:100'],
            'mother_name_extension' => ['nullable', 'string', 'max:20'],
            // Section III. Educational Background (elem, secondary, voc, college, grad)
            ...collect(['elem', 'secondary', 'voc', 'college', 'grad'])->flatMap(function ($prefix) {
                return [
                    "{$prefix}_school" => ['nullable', 'string', 'max:255'],
                    "{$prefix}_degree_course" => ['nullable', 'string', 'max:255'],
                    "{$prefix}_period_from" => ['nullable', 'string', 'max:20'],
                    "{$prefix}_period_to" => ['nullable', 'string', 'max:20'],
                    "{$prefix}_highest_level_units" => ['nullable', 'string', 'max:255'],
                    "{$prefix}_year_graduated" => ['nullable', 'string', 'max:10'],
                    "{$prefix}_scholarship_honors" => ['nullable', 'string', 'max:255'],
                ];
            })->all(),
            // Section IV. Civil Service Eligibility (repeatable)
            'eligibility' => ['nullable', 'array'],
            'eligibility.*.eligibility_type' => ['nullable', 'string', 'max:500'],
            'eligibility.*.rating' => ['nullable', 'string', 'max:50'],
            'eligibility.*.date_exam_conferment' => ['nullable', 'date'],
            'eligibility.*.license_valid_until' => ['nullable', 'date'],
            'eligibility.*.place_exam_conferment' => ['nullable', 'string', 'max:255'],
            'eligibility.*.license_number' => ['nullable', 'string', 'max:100'],
            // Section V. Work Experience (repeatable)
            'work' => ['nullable', 'array'],
            'work.*.from_date' => ['nullable', 'date'],
            'work.*.to_date' => ['nullable', 'date'],
            'work.*.position_title' => ['nullable', 'string', 'max:255'],
            'work.*.department_agency' => ['nullable', 'string', 'max:500'],
            'work.*.status_of_appointment' => ['nullable', 'string', 'max:100'],
            'work.*.govt_service_yn' => ['nullable', Rule::in(['', 'Y', 'N'])],
            // Section VI. Voluntary Work (repeatable)
            'voluntary' => ['nullable', 'array'],
            'voluntary.*.conducted_sponsored_by' => ['nullable', 'string', 'max:500'],
            'voluntary.*.inclusive_dates_from' => ['nullable', 'date'],
            'voluntary.*.inclusive_dates_to' => ['nullable', 'date'],
            'voluntary.*.position_nature_of_work' => ['nullable', 'string', 'max:255'],
            'voluntary.*.number_of_hours' => ['nullable', 'numeric', 'min:0'],
            // Section VII. Learning and Development (repeatable)
            'learning_development' => ['nullable', 'array'],
            'learning_development.*.organization_name_address' => ['nullable', 'string', 'max:500'],
            'learning_development.*.title_of_ld' => ['nullable', 'string', 'max:500'],
            'learning_development.*.type_of_ld' => ['nullable', 'string', 'max:100'],
            'learning_development.*.type_of_ld_specify' => ['nullable', 'string', 'max:100'],
            'learning_development.*.number_of_hours' => ['nullable', 'numeric', 'min:0'],
            'learning_development.*.inclusive_dates_from' => ['nullable', 'date'],
            'learning_development.*.inclusive_dates_to' => ['nullable', 'date'],
            // Section VIII. Other Information
            'special_skills_hobbies' => ['nullable', 'string', 'max:1000'],
            'non_academic_distinctions' => ['nullable', 'string', 'max:1000'],
            'membership_in_associations' => ['nullable', 'string', 'max:1000'],
            // Page 4 – Questions 34–40 (Y/N + details)
            'admin_offense_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'admin_offense_details' => ['nullable', 'string', 'max:1000'],
            'related_third_degree_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'related_fourth_degree_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'related_authority_details' => ['nullable', 'string', 'max:1000'],
            'indigenous_group_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'indigenous_group_specify' => ['nullable', 'string', 'max:1000'],
            'pwd_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'pwd_id_no' => ['nullable', 'string', 'max:255'],
            'solo_parent_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'solo_parent_id_no' => ['nullable', 'string', 'max:255'],
            'separated_from_service_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'separated_from_service_details' => ['nullable', 'string', 'max:1000'],
            'immigrant_resident_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'immigrant_resident_details' => ['nullable', 'string', 'max:255'],
            'candidate_election_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'candidate_election_details' => ['nullable', 'string', 'max:1000'],
            'resigned_campaign_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'resigned_campaign_details' => ['nullable', 'string', 'max:1000'],
            'criminally_charged_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'criminally_charged_date_filed' => ['nullable', 'string', 'max:100'],
            'criminally_charged_status' => ['nullable', 'string', 'max:255'],
            'criminally_charged_details' => ['nullable', 'string', 'max:1000'],
            'convicted_yn' => ['nullable', Rule::in(['Y', 'N', ''])],
            'convicted_details' => ['nullable', 'string', 'max:1000'],
            // 41. References
            'ref1_name' => ['nullable', 'string', 'max:255'],
            'ref1_address' => ['nullable', 'string', 'max:1000'],
            'ref1_contact' => ['nullable', 'string', 'max:255'],
            'ref2_name' => ['nullable', 'string', 'max:255'],
            'ref2_address' => ['nullable', 'string', 'max:1000'],
            'ref2_contact' => ['nullable', 'string', 'max:255'],
            'ref3_name' => ['nullable', 'string', 'max:255'],
            'ref3_address' => ['nullable', 'string', 'max:1000'],
            'ref3_contact' => ['nullable', 'string', 'max:255'],
            // 42. Declaration & Government ID
            'govt_id_type' => ['nullable', 'string', 'max:100'],
            'govt_id_number' => ['nullable', 'string', 'max:100'],
            'govt_id_place_date_issue' => ['nullable', 'string', 'max:255'],
            'date_accomplished' => ['nullable', 'date'],
        ]);

        $childrenData = null;
        if ($request->filled('children_data')) {
            $decoded = json_decode($request->children_data, true);
            $childrenData = is_array($decoded) ? $decoded : null;
        }
        unset($validated['children_data'], $validated['eligibility'], $validated['work'], $validated['voluntary'], $validated['learning_development']);
        if (($validated['civil_status'] ?? '') === 'single') {
            foreach (['spouse_surname', 'spouse_name_extension', 'spouse_first_name', 'spouse_middle_name', 'spouse_occupation', 'spouse_employer_business_name', 'spouse_business_address', 'spouse_telephone'] as $key) {
                $validated[$key] = null;
            }
        }
        $pds->fill($validated);
        $pds->user_id = $targetUser->id;
        if ($childrenData !== null) {
            $pds->children_data = $childrenData;
        }
        $pds->save();

        $this->syncCivilServiceEligibilities($pds, $request->input('eligibility', []));
        $this->syncWorkExperiences($pds, $request->input('work', []));
        $this->syncVoluntaryWorks($pds, $request->input('voluntary', []));
        $this->syncLearningDevelopments($pds, $request->input('learning_development', []));

        return response()->json(['success' => true]);
    }

    /**
     * JSON: trainings for the user to import into PDS L&D section (own or personnel).
     */
    public function importableTrainings(Request $request, ?User $user = null): JsonResponse
    {
        $targetUser = $user ?? $request->user();
        $this->authorize('view', $targetUser);

        $trainings = $targetUser->trainings()
            ->orderBy('trainings.start_date', 'desc')
            ->get();

        $data = $trainings->map(function ($training) {
            return [
                'organization_name_address' => $training->provider ?? '',
                'title_of_ld' => $training->title ?? '',
                'type_of_ld' => $training->type_of_ld ?? 'Other',
                'type_of_ld_specify' => $training->type_of_ld_specify ?? '',
                'number_of_hours' => $training->hours,
                'inclusive_dates_from' => $training->start_date ? \Carbon\Carbon::parse($training->start_date)->format('Y-m-d') : null,
                'inclusive_dates_to' => $training->end_date ? \Carbon\Carbon::parse($training->end_date)->format('Y-m-d') : null,
            ];
        })->values()->all();

        return response()->json(['data' => $data]);
    }

    private function syncCivilServiceEligibilities(PersonalDataSheet $pds, array $rows): void
    {
        $pds->civilServiceEligibilities()->delete();
        $rows = array_values($rows);
        foreach ($rows as $i => $row) {
            $row = is_array($row) ? $row : [];
            $pds->civilServiceEligibilities()->create([
                'eligibility_type' => $row['eligibility_type'] ?? null,
                'rating' => $row['rating'] ?? null,
                'date_exam_conferment' => ! empty($row['date_exam_conferment']) ? $row['date_exam_conferment'] : null,
                'license_valid_until' => ! empty($row['license_valid_until']) ? $row['license_valid_until'] : null,
                'place_exam_conferment' => $row['place_exam_conferment'] ?? null,
                'license_number' => $row['license_number'] ?? null,
                'sort_order' => $i,
            ]);
        }
    }

    private function syncWorkExperiences(PersonalDataSheet $pds, array $rows): void
    {
        $pds->workExperiences()->delete();
        $rows = array_values($rows);
        foreach ($rows as $i => $row) {
            $row = is_array($row) ? $row : [];
            $pds->workExperiences()->create([
                'from_date' => ! empty($row['from_date']) ? $row['from_date'] : null,
                'to_date' => ! empty($row['to_date']) ? $row['to_date'] : null,
                'position_title' => $row['position_title'] ?? null,
                'department_agency' => $row['department_agency'] ?? null,
                'status_of_appointment' => $row['status_of_appointment'] ?? null,
                'govt_service_yn' => isset($row['govt_service_yn']) && $row['govt_service_yn'] !== '' ? $row['govt_service_yn'] : null,
                'sort_order' => $i,
            ]);
        }
    }

    private function syncVoluntaryWorks(PersonalDataSheet $pds, array $rows): void
    {
        $pds->voluntaryWorks()->delete();
        $rows = array_values($rows);
        foreach ($rows as $i => $row) {
            $row = is_array($row) ? $row : [];
            $pds->voluntaryWorks()->create([
                'conducted_sponsored_by' => $row['conducted_sponsored_by'] ?? null,
                'inclusive_dates_from' => ! empty($row['inclusive_dates_from']) ? $row['inclusive_dates_from'] : null,
                'inclusive_dates_to' => ! empty($row['inclusive_dates_to']) ? $row['inclusive_dates_to'] : null,
                'position_nature_of_work' => $row['position_nature_of_work'] ?? null,
                'number_of_hours' => isset($row['number_of_hours']) && $row['number_of_hours'] !== '' ? $row['number_of_hours'] : null,
                'sort_order' => $i,
            ]);
        }
    }

    private function syncLearningDevelopments(PersonalDataSheet $pds, array $rows): void
    {
        $pds->learningDevelopments()->delete();
        $rows = array_values($rows);
        foreach ($rows as $i => $row) {
            $row = is_array($row) ? $row : [];
            // Skip empty rows (all fields are empty/null)
            $isEmpty = empty($row['title_of_ld'])
                && empty($row['organization_name_address'])
                && empty($row['type_of_ld'])
                && empty($row['type_of_ld_specify'])
                && (empty($row['number_of_hours']) || $row['number_of_hours'] === '')
                && empty($row['inclusive_dates_from'])
                && empty($row['inclusive_dates_to']);
            if ($isEmpty) {
                continue;
            }
            $pds->learningDevelopments()->create([
                'organization_name_address' => $row['organization_name_address'] ?? null,
                'title_of_ld' => $row['title_of_ld'] ?? null,
                'type_of_ld' => $row['type_of_ld'] ?? null,
                'type_of_ld_specify' => $row['type_of_ld_specify'] ?? null,
                'number_of_hours' => isset($row['number_of_hours']) && $row['number_of_hours'] !== '' ? $row['number_of_hours'] : null,
                'inclusive_dates_from' => ! empty($row['inclusive_dates_from']) ? $row['inclusive_dates_from'] : null,
                'inclusive_dates_to' => ! empty($row['inclusive_dates_to']) ? $row['inclusive_dates_to'] : null,
                'sort_order' => $i,
            ]);
        }
    }
}

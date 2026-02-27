<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class PersonalDataSheet extends Model
{
    protected $table = 'personal_data_sheets';

    protected $fillable = [
        'user_id',
        'surname',
        'name_extension',
        'first_name',
        'middle_name',
        'date_of_birth',
        'place_of_birth',
        'sex',
        'civil_status',
        'civil_status_other',
        'height',
        'weight',
        'blood_type',
        'umid_id',
        'pagibig_id',
        'philhealth_no',
        'philsys_number',
        'tin_no',
        'agency_employee_no',
        'date_of_appointment',
        'citizenship',
        'dual_citizenship_type',
        'dual_citizenship_country',
        'residential_house_no',
        'residential_street',
        'residential_subdivision',
        'residential_barangay',
        'residential_city',
        'residential_province',
        'residential_zip',
        'permanent_house_no',
        'permanent_street',
        'permanent_subdivision',
        'permanent_barangay',
        'permanent_city',
        'permanent_province',
        'permanent_zip',
        'telephone',
        'mobile',
        'email_address',
        'photo_path',
        // Section II. Family Background
        'spouse_surname',
        'spouse_first_name',
        'spouse_middle_name',
        'spouse_name_extension',
        'spouse_occupation',
        'spouse_employer_business_name',
        'spouse_business_address',
        'spouse_telephone',
        'children_names',
        'father_surname',
        'father_first_name',
        'father_middle_name',
        'father_name_extension',
        'mother_surname',
        'mother_first_name',
        'mother_middle_name',
        'mother_name_extension',
        // Section III. Educational Background
        'elem_school',
        'elem_degree_course',
        'elem_period_from',
        'elem_period_to',
        'elem_highest_level_units',
        'elem_year_graduated',
        'elem_scholarship_honors',
        'secondary_school',
        'secondary_degree_course',
        'secondary_period_from',
        'secondary_period_to',
        'secondary_highest_level_units',
        'secondary_year_graduated',
        'secondary_scholarship_honors',
        'voc_school',
        'voc_degree_course',
        'voc_period_from',
        'voc_period_to',
        'voc_highest_level_units',
        'voc_year_graduated',
        'voc_scholarship_honors',
        'college_school',
        'college_degree_course',
        'college_period_from',
        'college_period_to',
        'college_highest_level_units',
        'college_year_graduated',
        'college_scholarship_honors',
        'grad_school',
        'grad_degree_course',
        'grad_period_from',
        'grad_period_to',
        'grad_highest_level_units',
        'grad_year_graduated',
        'grad_scholarship_honors',
        // Section VIII. Other Information (31–33)
        'special_skills_hobbies',
        'non_academic_distinctions',
        'membership_in_associations',
        // Page 4 – Questions 34–40 (Y/N + details)
        'admin_offense_yn',
        'admin_offense_details',
        'related_third_degree_yn',
        'related_fourth_degree_yn',
        'related_authority_details',
        'indigenous_group_yn',
        'indigenous_group_specify',
        'pwd_yn',
        'pwd_id_no',
        'solo_parent_yn',
        'solo_parent_id_no',
        'separated_from_service_yn',
        'separated_from_service_details',
        'immigrant_resident_yn',
        'immigrant_resident_details',
        'candidate_election_yn',
        'candidate_election_details',
        'resigned_campaign_yn',
        'resigned_campaign_details',
        'criminally_charged_yn',
        'criminally_charged_date_filed',
        'criminally_charged_status',
        'criminally_charged_details',
        'convicted_yn',
        'convicted_details',
        // 41. References
        'ref1_name',
        'ref1_address',
        'ref1_contact',
        'ref2_name',
        'ref2_address',
        'ref2_contact',
        'ref3_name',
        'ref3_address',
        'ref3_contact',
        // 42. Declaration & Government ID
        'govt_id_type',
        'govt_id_number',
        'govt_id_place_date_issue',
        'date_accomplished',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_appointment' => 'date',
        'date_accomplished' => 'date',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'children_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function civilServiceEligibilities(): HasMany
    {
        return $this->hasMany(CivilServiceEligibility::class)->orderBy('sort_order');
    }

    public function workExperiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class)->orderBy('sort_order');
    }

    public function voluntaryWorks(): HasMany
    {
        return $this->hasMany(PdsVoluntaryWork::class)->orderBy('sort_order');
    }

    public function learningDevelopments(): HasMany
    {
        return $this->hasMany(PdsLearningDevelopment::class)->orderBy('sort_order');
    }

    /** Public URL for the passport-sized photo (4.5×3.5 cm). */
    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo_path)) {
            return null;
        }
        return Storage::disk('public')->exists($this->photo_path)
            ? Storage::disk('public')->url($this->photo_path)
            : null;
    }
}

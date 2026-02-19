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
        'mother_surname',
        'mother_first_name',
        'mother_middle_name',
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
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_appointment' => 'date',
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

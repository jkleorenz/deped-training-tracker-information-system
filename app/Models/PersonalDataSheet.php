<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_appointment' => 'date',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

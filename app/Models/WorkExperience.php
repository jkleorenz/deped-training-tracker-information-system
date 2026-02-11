<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkExperience extends Model
{
    protected $table = 'work_experiences';

    protected $fillable = [
        'personal_data_sheet_id',
        'from_date',
        'to_date',
        'position_title',
        'department_agency',
        'status_of_appointment',
        'govt_service_yn',
        'sort_order',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    public function personalDataSheet(): BelongsTo
    {
        return $this->belongsTo(PersonalDataSheet::class);
    }
}

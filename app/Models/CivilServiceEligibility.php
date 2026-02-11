<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CivilServiceEligibility extends Model
{
    protected $table = 'civil_service_eligibilities';

    protected $fillable = [
        'personal_data_sheet_id',
        'eligibility_type',
        'rating',
        'date_exam_conferment',
        'place_exam_conferment',
        'license_number',
        'license_valid_until',
        'sort_order',
    ];

    protected $casts = [
        'date_exam_conferment' => 'date',
        'license_valid_until' => 'date',
    ];

    public function personalDataSheet(): BelongsTo
    {
        return $this->belongsTo(PersonalDataSheet::class);
    }
}

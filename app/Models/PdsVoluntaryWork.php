<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdsVoluntaryWork extends Model
{
    protected $table = 'pds_voluntary_works';

    protected $fillable = [
        'personal_data_sheet_id',
        'conducted_sponsored_by',
        'inclusive_dates_from',
        'inclusive_dates_to',
        'position_nature_of_work',
        'number_of_hours',
        'sort_order',
    ];

    protected $casts = [
        'inclusive_dates_from' => 'date',
        'inclusive_dates_to' => 'date',
    ];

    public function personalDataSheet(): BelongsTo
    {
        return $this->belongsTo(PersonalDataSheet::class);
    }
}

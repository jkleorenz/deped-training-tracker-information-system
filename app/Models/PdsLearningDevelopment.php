<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdsLearningDevelopment extends Model
{
    protected $table = 'pds_learning_developments';

    protected $fillable = [
        'personal_data_sheet_id',
        'organization_name_address',
        'title_of_ld',
        'type_of_ld',
        'type_of_ld_specify',
        'number_of_hours',
        'inclusive_dates_from',
        'inclusive_dates_to',
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

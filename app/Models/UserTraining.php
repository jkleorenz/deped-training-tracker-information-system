<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTraining extends Model
{
    protected $table = 'user_trainings';

    protected $fillable = [
        'user_id',
        'training_id',
        'attended_date',
        'remarks',
    ];

    protected $casts = [
        'attended_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }
}

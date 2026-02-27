<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type_of_ld',
        'type_of_ld_specify',
        'provider',
        'venue',
        'start_date',
        'end_date',
        'hours',
        'description',
        'certificate_number',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Personnel who attended this training.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_trainings')
            ->withPivot(['attended_date', 'remarks'])
            ->withTimestamps();
    }

    public function userTrainings()
    {
        return $this->hasMany(UserTraining::class);
    }
}

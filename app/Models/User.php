<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_SUB_ADMIN = 'sub-admin';
    public const ROLE_PERSONNEL = 'personnel';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'employee_id',
        'designation',
        'department',
        'school',
        'theme',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSubAdmin(): bool
    {
        return $this->role === self::ROLE_SUB_ADMIN;
    }

    public function isPersonnel(): bool
    {
        return $this->role === self::ROLE_PERSONNEL;
    }

    /** Admin or sub-admin (mini admin). */
    public function isAdminOrSubAdmin(): bool
    {
        return $this->isAdmin() || $this->isSubAdmin();
    }

    /**
     * Trainings attended (many-to-many with pivot data).
     */
    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'user_trainings')
            ->withPivot(['attended_date', 'remarks'])
            ->withTimestamps();
    }

    /**
     * Pivot records for trainings (when you need to query pivot directly).
     */
    public function userTrainings()
    {
        return $this->hasMany(UserTraining::class);
    }

    /**
     * Personal Data Sheet (CS Form 212) — one per user.
     */
    public function personalDataSheet()
    {
        return $this->hasOne(PersonalDataSheet::class);
    }
}

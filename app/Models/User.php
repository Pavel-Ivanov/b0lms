<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_department_id',
        'company_position_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
//        return true;
        if ($panel->getId() === 'sadmin') {
            return $this->hasRole([
                'Superadmin',
                'Администратор',
            ]);
        }

        if ($panel->getId() === 'admin') {
            return $this->hasRole([
                'Superadmin',
                'Администратор',
            ]);
        }

        if ($panel->getId() === 'teacher') {
            return $this->hasRole([
                'Superadmin',
                'Администратор',
                'Преподаватель',
            ]);
        }

        if ($panel->getId() === 'intern') {
            return $this->hasRole([
                'Superadmin',
                'Администратор',
                'Преподаватель',
                'Студент',
            ]);
        }

        return false;
    }


    public function companyDepartment(): BelongsTo
    {
        return $this->belongsTo(CompanyDepartment::class);
    }

    public function companyPosition(): BelongsTo
    {
        return $this->belongsTo(CompanyPosition::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function courses(): HasManyThrough
    {
        return $this->hasManyThrough(Course::class, Enrollment::class, 'user_id', 'id', 'id', 'course_id');
    }

    public function completedLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class)->published();
    }

}

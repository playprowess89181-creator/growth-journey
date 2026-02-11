<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'onboarding_data',
        'onboarding_completed_at',
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
            'onboarding_data' => 'array',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    /**
     * Get the community groups this user is a member of.
     */
    public function communityGroups()
    {
        return $this->belongsToMany(CommunityGroup::class, 'community_group_user')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    /**
     * Get the reports made by this user.
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get the reports reviewed by this user (admin).
     */
    public function reviewedReports()
    {
        return $this->hasMany(Report::class, 'reviewed_by');
    }
}

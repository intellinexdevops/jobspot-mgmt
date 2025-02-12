<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements JWTSubject, FilamentUser, HasName, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'nickname',
        'email',
        'password',
        'avatar',
        'gender',
        'birthday',
        'bio',
        'mobile',
        'access_token',
        'push_token',
        'verification',
        'location_id',
        'status'
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


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        if ($panel->getId() === "admin") {
            return str_ends_with($this->email, '@gmail.com') && $this->hasVerifiedEmail();
        }
        return false;
    }
    public function getFilamentName(): string
    {
        return "{$this->nickname}";
    }

    public function getFilamentAvatarUrl(): string|null
    {
        return Storage::url($this->avatar);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}

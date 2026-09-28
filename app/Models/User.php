<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_BENDAHARA = 'bendahara';
    public const ROLE_PEWAWANCARA = 'pewawancara';
    public const ROLE_KEPALA_SEKOLAH = 'kepala_sekolah';
    public const ROLE_CALON_SISWA = 'calon_siswa';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'phone',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user is Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is Bendahara.
     */
    public function isBendahara(): bool
    {
        return $this->role === self::ROLE_BENDAHARA;
    }

    /**
     * Check if user is Pewawancara.
     */
    public function isPewawancara(): bool
    {
        return $this->role === self::ROLE_PEWAWANCARA;
    }

    /**
     * Check if user is Kepala Sekolah.
     */
    public function isKepalaSekolah(): bool
    {
        return $this->role === self::ROLE_KEPALA_SEKOLAH;
    }

    /**
     * Check if user is Calon Siswa.
     */
    public function isCalonSiswa(): bool
    {
        return $this->role === self::ROLE_CALON_SISWA;
    }

    /**
     * Check if user has any of the specified roles.
     *
     * @param string|array $roles
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    /**
     * Get target dashboard route name based on user role.
     */
    public function getDashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'admin.dashboard',
            self::ROLE_BENDAHARA => 'bendahara.dashboard',
            self::ROLE_PEWAWANCARA => 'pewawancara.dashboard',
            self::ROLE_KEPALA_SEKOLAH => 'kepala-sekolah.dashboard',
            self::ROLE_CALON_SISWA => 'calon-siswa.dashboard',
            default => 'login',
        };
    }

    /**
     * Human-readable role label.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_BENDAHARA => 'Bendahara',
            self::ROLE_PEWAWANCARA => 'Pewawancara',
            self::ROLE_KEPALA_SEKOLAH => 'Kepala Sekolah',
            self::ROLE_CALON_SISWA => 'Calon Siswa',
            default => ucfirst(str_replace('_', ' ', (string) $this->role)),
        };
    }

    /**
     * Relationship to CalonSiswa profile.
     */
    public function calonSiswa(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CalonSiswa::class, 'user_id');
    }
}

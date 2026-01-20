<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasHashId;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasHashId;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'perusahaan_id',
        'name',
        'email',
        'no_whatsapp',
        'password',
        'role',
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
     * The attributes that should be appended to arrays.
     *
     * @var array
     */
    protected $appends = ['hash_id'];

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

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class);
    }

    public function patrolis()
    {
        return $this->hasMany(Patroli::class);
    }

    // Role Constants
    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SECURITY_OFFICER = 'security_officer';
    public const ROLE_OFFICE_EMPLOYEE = 'office_employee';
    public const ROLE_MANAGER_PROJECT = 'manager_project';
    public const ROLE_ADMIN_PROJECT = 'admin_project';
    public const ROLE_ADMIN_BRANCH = 'admin_branch';
    public const ROLE_FINANCE_BRANCH = 'finance_branch';
    public const ROLE_ADMIN_HSSE = 'admin_hsse';
    public const ROLE_PETUGAS = 'petugas'; // Backward compatibility

    // Role Check Methods
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSecurityOfficer(): bool
    {
        return $this->role === self::ROLE_SECURITY_OFFICER;
    }

    public function isOfficeEmployee(): bool
    {
        return $this->role === self::ROLE_OFFICE_EMPLOYEE;
    }

    public function isManagerProject(): bool
    {
        return $this->role === self::ROLE_MANAGER_PROJECT;
    }

    public function isAdminProject(): bool
    {
        return $this->role === self::ROLE_ADMIN_PROJECT;
    }

    public function isAdminBranch(): bool
    {
        return $this->role === self::ROLE_ADMIN_BRANCH;
    }

    public function isFinanceBranch(): bool
    {
        return $this->role === self::ROLE_FINANCE_BRANCH;
    }

    public function isAdminHSSE(): bool
    {
        return $this->role === self::ROLE_ADMIN_HSSE;
    }

    public function isPetugas(): bool
    {
        return $this->role === self::ROLE_PETUGAS;
    }

    // Helper method untuk check multiple roles
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }

    // Get role display name
    public function getRoleDisplayName(): string
    {
        return match($this->role) {
            self::ROLE_SUPERADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_SECURITY_OFFICER => 'Security Officer',
            self::ROLE_OFFICE_EMPLOYEE => 'Office Employee',
            self::ROLE_MANAGER_PROJECT => 'Manager Project',
            self::ROLE_ADMIN_PROJECT => 'Admin Project',
            self::ROLE_ADMIN_BRANCH => 'Admin Branch',
            self::ROLE_FINANCE_BRANCH => 'Finance Branch',
            self::ROLE_ADMIN_HSSE => 'Admin HSSE',
            self::ROLE_PETUGAS => 'Petugas',
            default => ucfirst($this->role),
        };
    }

    /**
     * Get project IDs yang bisa diakses user melalui jabatan
     * IMPORTANT: Tidak menggunakan global scope untuk menghindari circular dependency
     */
    public function getAccessibleProjectIds(): array
    {
        if ($this->isSuperAdmin()) {
            // Superadmin bisa akses semua project di perusahaan mereka
            return Project::withoutGlobalScope('project_access')
                ->where('perusahaan_id', $this->perusahaan_id)
                ->pluck('id')->toArray();
        }
        
        // PRIORITAS 1: Gunakan project_id langsung dari karyawan (lebih akurat)
        if ($this->karyawan && $this->karyawan->project_id) {
            return [$this->karyawan->project_id];
        }
        
        // FALLBACK: Gunakan project dari jabatan (untuk backward compatibility)
        if ($this->karyawan && $this->karyawan->jabatan) {
            return $this->karyawan->jabatan->projects()
                ->withoutGlobalScope('project_access')
                ->pluck('projects.id')->toArray();
        }
        
        return [];
    }
    
    /**
     * Get project pertama yang bisa diakses user
     */
    public function getFirstAccessibleProject(): ?Project
    {
        // PRIORITAS 1: Gunakan project_id langsung dari karyawan
        if ($this->karyawan && $this->karyawan->project_id) {
            return Project::withoutGlobalScope('project_access')
                ->find($this->karyawan->project_id);
        }
        
        // FALLBACK: Gunakan method lama
        $projectIds = $this->getAccessibleProjectIds();
        
        if (!empty($projectIds)) {
            return Project::withoutGlobalScope('project_access')
                ->whereIn('id', $projectIds)->first();
        }
        
        return null;
    }

    // Get all available roles
    public static function getAllRoles(): array
    {
        return [
            self::ROLE_SUPERADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_SECURITY_OFFICER => 'Security Officer',
            self::ROLE_OFFICE_EMPLOYEE => 'Office Employee',
            self::ROLE_MANAGER_PROJECT => 'Manager Project',
            self::ROLE_ADMIN_PROJECT => 'Admin Project',
            self::ROLE_ADMIN_BRANCH => 'Admin Branch',
            self::ROLE_FINANCE_BRANCH => 'Finance Branch',
            self::ROLE_ADMIN_HSSE => 'Admin HSSE',
        ];
    }
}

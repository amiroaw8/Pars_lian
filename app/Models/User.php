<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\RoleLabels;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'phone',
        'password',
        'email',
        'province',
        'city',
        'street',
        'alley',
        'plate',
        'postal_code',
        'is_active',
        'two_factor_code',
        'two_factor_expires_at',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is technician
     */
    public function isTechnician(): bool
    {
        return $this->hasRole('technician');
    }

    /**
     * Check if user is receptionist
     */
    public function isReceptionist(): bool
    {
        return $this->hasRole('receptionist');
    }

    /**
     * Check if user is warehouse manager
     */
    public function isWarehouseManager(): bool
    {
        return $this->hasRole('warehouse');
    }

    /**
     * Check if user is accountant
     */
    public function isAccountant(): bool
    {
        return $this->hasRole('accountant');
    }

    /**
     * Check if user is customer
     */
    public function isCustomer(): bool
    {
        return $this->hasRole('customer') || $this->roles->isEmpty();
    }

    /**
     * Check if user is employee (any staff role)
     */
    public function isEmployee(): bool
    {
        return $this->hasAnyRole(['admin', 'technician', 'receptionist', 'warehouse', 'accountant', 'super_admin', 'employee']);
    }

    /**
     * Check if user can access admin panel
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->isEmployee() && ! $this->hasRole('customer');
    }

    /**
     * Check if user can edit service orders
     */
    public function canEditServiceOrders(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->isReceptionist() || $this->isTechnician() || $this->isAccountant();
    }

    /**
     * Check if user can manage repairs
     */
    public function canManageRepairs(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->isTechnician();
    }

    /**
     * Check if user can manage inventory
     */
    public function canManageInventory(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->isWarehouseManager();
    }

    public function canManageProducts(): bool
    {
        return $this->isSuperAdmin()
            || $this->isAdmin()
            || $this->isWarehouseManager()
            || $this->isReceptionist()
            || $this->isAccountant();
    }

    /**
     * Check if user can manage accounting
     */
    public function canManageAccounting(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->isAccountant();
    }

    public function canAccessPos(): bool
    {
        return $this->canManageAccounting()
            || $this->isReceptionist()
            || $this->isWarehouseManager();
    }

    /**
     * Check if user can manage customers
     */
    public function canManageCustomers(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->isReceptionist();
    }

    /**
     * Get user role display name
     */
    public function getRoleDisplayName(): string
    {
        if ($this->roles->isEmpty()) {
            return RoleLabels::label('customer');
        }

        return $this->roles
            ->map(fn ($role) => RoleLabels::label($role->name))
            ->unique()
            ->implode('، ');
    }

    /**
     * Get the customer associated with the user.
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Service orders assigned to this user as technician.
     */
    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class, 'technician_id');
    }

    /**
     * Activity logs recorded by this user.
     */
    public function orderLogs(): HasMany
    {
        return $this->hasMany(OrderLog::class);
    }

    /**
     * Generate and send 2FA code
     */
    public function generateTwoFactorCode(): void
    {
        $this->timestamps = false; // Prevent updating updated_at
        $this->two_factor_code = (string) random_int(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(config('two_factor.ttl', 2));
        $this->save();

        // Send SMS via service
        if (! \App\Support\SmsNotifications::isTwoFactorEnabled()) {
            return;
        }

        $smsService = app(\App\Services\SMSService::class);
        $smsService->sendSMS($this->phone, \App\Support\SmsNotifications::prepareTwoFactorMessage($this->two_factor_code));
    }

    /**
     * Reset 2FA code
     */
    public function resetTwoFactorCode(): void
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    /**
     * Check if user needs 2FA
     */
    public function needsTwoFactor(): bool
    {
        if (Setting::get('two_factor_enabled', '1') !== '1') {
            return false;
        }

        return $this->hasAnyRole(['admin', 'technician', 'receptionist', 'warehouse', 'accountant', 'super_admin']);
    }

    /**
     * Find the user instance for the given phone number.
     */
    public function findForPhone(string $phone): ?User
    {
        return $this->where('phone', $phone)->first();
    }
}

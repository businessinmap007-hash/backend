<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use willvincent\Rateable\Rateable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRolesAndAbilities, Rateable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
        'is_suspend',
        'action_code',
        'code',
        'logo',
        'cover',
        'image',
        'latitude',
        'longitude',
        'location_id',
        'category_id',
        'about',
        'paid_at',
        'account_type',   // business | client
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pin_code',
    ];
    /**
     * Casts
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_suspend' => 'boolean',
        'account_type' => 'string',
    ];

    /**
     * Automatically hash password
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] =
                Hash::needsRehash($value) ? Hash::make($value) : $value;
        }
    }

    public function hasPin(): bool
    {
        return !empty($this->pin_code);
    }

    public function checkPin(string $pin): bool
    {
        return Hash::check($pin, $this->pin_code);
    }

    public function setPin(string $pin)
    {
        $this->pin_code = Hash::make($pin);
        $this->save();
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
    public function social()
    {
        return $this->hasOne(Social::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function options()
    {
        return $this->belongsToMany(Option::class, 'option_user');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Escrow relations
    public function escrowsSent()
    {
        return $this->hasMany(Escrow::class, 'from_user_id');
    }

    public function escrowsReceived()
    {
        return $this->hasMany(Escrow::class, 'to_user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | User Type Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Return account type (business | client | user)
     */
    public function userType(): string
    {
        return $this->account_type ?? 'user';
    }

    /**
     * Check if user is business owner
     */
    public function isBusiness(): bool
    {
        return $this->account_type === 'business';
    }

    /**
     * Check if user is client
     */
    public function isClient(): bool
    {
        return $this->account_type === 'client';
    }

    /*
    |--------------------------------------------------------------------------
    | Custom User Codes
    |--------------------------------------------------------------------------
    */

    /**
     * Generate unique action code (e.g. activation)
     */
    public static function actionCode($code)
    {
        return static::where('action_code', $code)->exists()
            ? rand(1000, 9999)
            : $code;
    }
    /**
     * Generate unique user code
     */
    public static function userCode($code)
    {
        return static::where('code', $code)->exists()
            ? rand(1000000000, 9999999999)
            : $code;
    }
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function setPinCodeAttribute($value)
    {
        if ($value) {
            $this->attributes['pin_code'] = Hash::make($value);
        }
    }

}


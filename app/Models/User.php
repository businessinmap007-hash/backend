<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
        'is_suspend',
        'is_user',
        'location_id',
        'action_code',
        'api_token',
        'code',
        'type',
        'logo',
        'cover',
        'image',
        'latitude',
        'longitude',
        'category_id',
        'about',
        'code', 'paid_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    /**
     * @return mixed
     * @@ Check if user have any roles.
     */


    public function hasAnyRoles()
    {
        if (auth()->check()) {

            if (auth()->user()->roles->count()) {
                return true;
            }
        } else {
            redirect(route('admin.login'));
        }
    }

    public function hasInstitutionRole()
    {

        if (auth()->check()) {
            $query = auth()->user()->roles()->where('name', 'institution')->first();

            if (!$query && !auth()->user()->isUserAdmin()) {
                return redirect(route('institution.home'));
            }

            return $query;
        } else {
            return redirect(route('institution.login'));
        }
    }


    public function hasVendorRole()
    {

        if (auth()->check()) {
            $query = auth()->user()->roles()->where('name', 'vendor')->first();

            if (!$query && !auth()->user()->isUserAdmin()) {
                return redirect(route('home'));
            }

            return $query;
        } else {
            return redirect(route('login'));
        }
    }


    public function isUserAdmin()
    {
        if (auth()->check()) {
            if (auth()->user()->is_user == "admin") {
                return true;
            }
        }
    }

    /**
     * Hash password
     * @param $input
     */
    public function setPasswordAttribute($input)
    {
        if ($input)
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
    }


    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }


    public static function userCode($code)
    {

        $rand = User::where('code', $code)->first();
        if ($rand) {
            return $randomCode = rand(1000000000, 9999999999);
        } else {
            return $code;
        }
    }


    public static function actionCode($code)
    {

        $rand = User::where('action_code', $code)->first();
        if ($rand || $rand == '') {
            return $randomCode = rand(1000, 9999);
        } else {
            return $code;
        }
    }

    /**
     * @param $query
     * @param $api_token
     * @return mixed
     */

    public function scopeIsActive($query, $phone)
    {
        if ($phone != '') {
            $query->where('phone', $phone);
        }
        return $query->first();
    }

    public function scopePilgrim($query)
    {
        $query->where('is_user', 1);
        return $query;
    }


    public function devices()
    {
        return $this->hasMany(Device::class);
    }


    public function profile()
    {
        return $this->hasOne(Profile::class);
    }


    public function companies()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public static function scopeByToken($query, $token)
    {

        if ($token != '') {
            $query->where('api_token', $token);
        }
        return $query->first();
    }

    public static function scopeById($query, $id)
    {

        if ($id != '') {
            $query->where('id', $id);
        }
        return $query->first();
    }

    public function ratings()
    {
        return $this->morphMany('willvincent\Rateable\Rating', 'rateable');
    }


    public function images()
    {
        return $this->morphMany('App\Models\Image', 'imageable');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }


    public function comments()
    {
        return $this->hasMany(Comment::class);
    }


    public function likes()
    {
        return $this->hasMany(Like::class);
    }


    public function support()
    {
        return $this->hasMany(Support::class);
    }

    /**
     * @return $this
     * @
     */

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function city()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }


    public function getCity()
    {
        return $this->belongsTo(CityTranslation::class, 'city_id', 'city_id')->where('locale', app()->getLocale());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function company()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }


    public function orders()
    {
        return $this->hasMany(Order::class, 'company_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }


    public function generalUserInfo()
    {

        $generalInfo = [
            'id' => (int)$this->id,
            'api_token' => (string)$this->api_token,
            'username' => (string)$this->name != null ? $this->name : "",
            'company_name' => $this->company_name != null ? $this->company_name : "",
            'phone' => $this->phone != null ? $this->phone : "",
            'email' => $this->email != null ? $this->email : "",
            'is_active' => $this->is_active ? (int)1 : 0,
            'is_suspend' => $this->is_suspend ? (int)1 : 0,
            // 'image' => $this->image != null ? $this->image : "",
            'image' => $this->image == '' ? request()->root() . '/public/assets/images/default.png' : $this->image,
            'address' => $this->address != null ? $this->address : "",
            'latitute' => $this->latitute != null ? $this->latitute : "",
            'longitute' => $this->longitute != null ? $this->longitute : "",
            'userType' => $this->userType(),
            'completed' => ($this->is_completed == 0) ? 0 : 1,
        ];

        $generalInfo = array_filter($generalInfo, function ($value) {
            return $value !== "" && !is_null($value);
        });

        return $generalInfo;
    }


    public function companyUserToArray()
    {
        $userCompany = [
            'commercial_registry_no' => $this->commercial_registry_no != null ? $this->commercial_registry_no : "",
            'transport_price_in' => $this->transport_price_in != null ? (int)$this->transport_price_in : "",
            'transport_price_out' => $this->transport_price_out != null ? (int)$this->transport_price_out : "",
            'order_per_day' => $this->order_per_day != null ? (int)$this->order_per_day : "",
            'duration' => $this->duration != null ? $this->duration : "",
            'company_logo' => $this->company_logo != null ? $this->company_logo : "",
            'civil_registry_no' => $this->civil_registry_no != null ? $this->civil_registry_no : "",
            'commercial_registry_image' => $this->commercial_registry_image != null ? $this->commercial_registry_image : "",
            'civil_registry_image' => $this->civil_registry_image != null ? $this->civil_registry_image : "",
            'products' => $this->products,
            'branch' => $this->branch()->with('city')->first(),


        ];


        $userCompany = array_filter($userCompany, function ($value) {
            return $value !== "" && !is_null($value);
        });

        return array_merge($this->generalUserInfo(), $userCompany);
    }


    public function clientToArray()
    {
        $userClient = [
            'commercial_registry_no' => $this->commercial_registry_no != null ? $this->commercial_registry_no : "",
            'civil_registry_no' => $this->civil_registry_no != null ? $this->civil_registry_no : "",
            'commercial_registry_image' => $this->commercial_registry_image != null ? $this->commercial_registry_image : "",
            'civil_registry_image' => $this->civil_registry_image != null ? $this->civil_registry_image : "",
            'fax_no' => $this->fax_no
        ];

        $userClient = array_filter($userClient, function ($value) {
            return $value !== "" && !is_null($value);
        });

        return array_merge($this->generalUserInfo(), $userClient);
    }

    public function driverUserToArray()
    {
        $userDriver = [

            'company_name' => $this->company != "" ? $this->company->company_name : "",
            'completed' => 1,
            'transporter_license_image' => $this->commercial_registry_image != null ? $this->commercial_registry_image : "",
            'driving_license' => $this->driving_license != null ? $this->driving_license : "",
            'traffic_license' => $this->traffic_license != null ? $this->traffic_license : "",
            'size' => $this->size,
            'city' => $this->city,


        ];


        $userDriver = array_filter($userDriver, function ($value) {
            return $value !== "" && !is_null($value);
        });

        return array_merge($this->generalUserInfo(), $userDriver);
    }


    public function driverInfo()
    {
        $driverInfo = [];
        array_merge($this->generalUserInfo(), $driverInfo);
    }

    public function userType()
    {
        $userType = 'user';
        if ($this->is_user == 1) {
            $userType = 'company';
        } elseif ($this->is_user == 2) {
            $userType = "driver";
        } elseif ($this->is_user == 3) {
            $userType = "client";
        }
        return $userType;
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }


    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }


    public function size()
    {
        return $this->belongsTo(Size::class, 'commercial_registry_no');
    }


    public function assignOrders()
    {
        return $this->hasMany(Track::class, 'driver_id');
    }




    //    public function transactions($id)
    //    {
    //        $transactions = Transaction::whereCompanyId($id)->get();
    //
    //        if ($transactions->count() > 0) {
    //
    //            $companyTransaction = collect($transactions)->where('type', 1);
    //            $companyTransactionApp = collect($transactions)->where('type', 4);
    //
    //
    //            $companyTransactionInApp = collect($transactions)->where('type', 2);
    //            $companyTransactionToApp = collect($transactions)->where('type', 3);
    //
    //
    //            $comp = collect($companyTransaction->pluck('amount'))->sum();
    //            $app = collect($companyTransactionApp->pluck('amount'))->sum();
    //
    //
    //            $forApp = collect($companyTransactionInApp->pluck('amount'))->sum();
    //            $fromComp = collect($companyTransactionToApp->pluck('amount'))->sum();
    //
    //            return [
    //                "companyDues" => (int)($comp - $app),
    //                "appDues" => (int)($forApp - $fromComp),
    //            ];
    //        } else {
    //            return "";
    //        }
    //    }


    function trips()
    {
        return $this->belongsToMany(Trip::class)->withPivot('bus_id');
    }


    public function getFirstName()
    {
        return $this->first_name;
    }

    public function isInFavorite($productId)
    {
        return $this->wishlists()->where('product_id', $productId)->first() ? true : false;
    }


    public function social()
    {
        return $this->hasOne(Social::class);
    }


    public function posts()
    {
        return $this->hasMany(Post::class);
    }


    public function applies()
    {
        return $this->hasMany(Apply::class);
    }


    public function albums()
    {
        return $this->hasMany(Album::class);
    }


    function followers()
    {
        return $this->belongsToMany(User::class, 'follow_user', 'user_id', 'follow_id');
    }

    function targets()
    {
        return $this->belongsToMany(User::class, 'target_user', 'user_id', 'target_id');
    }

    public function targetsReverse()
    {
        return $this->belongsToMany(User::class, 'target_user', 'target_id', 'user_id');
    }




    public function categoryFollows()
    {
        return $this->belongsToMany(Category::class, 'category_user');
    }


    public function categoryTargets()
    {
        return $this->belongsToMany(Category::class, 'category_target');
    }


    public function categoryTargetsReverse()
    {
        return $this->belongsToMany(Category::class, 'category_target', 'category_id', 'user_id');
    }



    public function sponsors()
    {
        return $this->hasMany(Sponsor::class);
    }



    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }


    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }


    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


    public function options()
    {
        return $this->belongsToMany(Option::class, 'option_user');
    }



    public function gifts()
    {
        return $this->hasOne(BusinessGift::class);
    }
}

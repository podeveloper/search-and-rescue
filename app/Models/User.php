<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\CacheUpdateTrait;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, SoftDeletes, Notifiable, HasRoles, CacheUpdateTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'full_name',
        'phone',
        'email',
        'last_login_at',
        'email_verified_at',
        'profile_photo',
        'date_of_birth',
        'password',
        'password_temp',
        'gender_id',
        'nationality_id',
        'education_level_id',
        'reference_name',
        'reference_id',
        'referral_source_id',
        'occupation_id',
        'occupation_text',
        'organisation_id',
        'organisation_text',
        'marital_status',
        'national_id_number',
        'passport_number',
        'is_admin',
        'kvkk',
        'note',
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

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->is_admin) {
            return true;
        }

        $panelRoles = [
            'candidate' => ['candidate'],
            'official' => ['official'],
            'coordinator' => ['coordinator'],
            'network' => ['network operator'],
            'stock' => ['stock operator'],
            'reference' => ['reference operator'],
        ];

        $panelRole = $panel->getId();

        if (isset($panelRoles[$panelRole])) {
            foreach ($panelRoles[$panelRole] as $roleName) {
                if ($this->hasRole($roleName)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected static function boot()
    {
        parent::boot();
        self::bootCacheUpdateTrait();

        static::creating(function ($user) {
            $user->full_name = $user->name . ' ' . $user->surname;
        });

        static::updating(function ($user) {
            if ($user->isDirty(['name', 'surname'])) {
                $user->full_name = $user->name . ' ' . $user->surname;
            }
        });
    }

    public function setFullNameAttribute($value)
    {
        $this->attributes['full_name'] = ucwords($value);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
    }

    public function setSurnameAttribute($value)
    {
        $this->attributes['surname'] = ucwords($value);
    }

    public function scopeMale(Builder $query)
    {
        return $query->where('gender_id', '=',1);
    }

    public function scopeFemale(Builder $query)
    {
        return $query->where('gender_id', '=',2);
    }

    public function scopeGenderNull(Builder $query)
    {
        return $query->whereNull('gender_id');
    }

    public function scopeWhereTag(Builder $query,$tag)
    {
        return $query->whereHas('tags', function ($query) use ($tag) {
            $query->where('name', $tag->name);
        });
    }

    public function canImpersonate()
    {
        return $this->is_admin;
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function reference(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reference_id');
    }

    public function referralSource(): BelongsTo
    {
        return $this->belongsTo(ReferralSource::class);
    }

    public function occupation(): BelongsTo
    {
        return $this->belongsTo(Occupation::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function languages(): MorphToMany
    {
        return $this->morphToMany(Language::class, 'languageable');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(UserCategory::class, 'user_category_user');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class);
    }

    public function certificates(): BelongsToMany
    {
        return $this->belongsToMany(Certificate::class)->withPivot([
            'issue_date',
            'expiry_date',
            'issuer',
            'url',
        ]);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class)->with([
            'country','city','district'
        ]);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function registrationQuestionAnswers(): HasMany
    {
        return $this->hasMany(RegistrationQuestionAnswer::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function driverLicences()
    {
        return $this->hasOne(DriverLicence::class, 'user_id');
    }

    public function healthProfile()
    {
        return $this->hasOne(HealthProfile::class);
    }
    public function firstAidCertificate()
    {
        return $this->hasOne(FirstAidCertificate::class, 'user_id');
    }

    public function radioCertificate()
    {
        return $this->hasOne(RadioCertificate::class, 'user_id');
    }

    public function forestFireFightingCertificate()
    {
        return $this->hasOne(ForestFireFightingCertificate::class, 'user_id');
    }
}

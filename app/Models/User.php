<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Traits\LogAllTraits;
use Filament\Forms\Components\TextInput;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasApiTokens,
        HasFactory,
        HasRoles,
        LogAllTraits,
        Notifiable,
        SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->required()
                ->unique('users', 'email')
                ->maxLength(255),
            // // Forms\Components\DateTimePicker::make('email_verified_at'),
            // TextInput::make('password')
            //     ->password()
            //     ->maxLength(255)
            //     ->hidden(fn (string $operation): bool => $operation === 'edit'),
        ];
    }

    protected static function booted()
    {
        static::created(function (User $user) {
            // Assign the user role by default
            // TODO: Make this configurable

            $user->assignRole(['user', 'Super Administrator']);
            event(new Registered($user));
        });
    }

    public function isAdministrator(): bool
    {
        return $this->hasRole('super-administrator') || $this->hasRole('system-administrator');
    }

    public function isUser(): bool
    {
        return $this->hasRole('user');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isUser();
    }
}

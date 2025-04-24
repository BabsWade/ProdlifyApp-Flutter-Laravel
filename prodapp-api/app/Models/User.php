<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }
    public function verifyEmail($id, Request $request)
{
    $user = User::findOrFail($id);

    // Vérifie si l'email est déjà vérifié
    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email déjà vérifié.'], 200);
    }

    // Marque l'email comme vérifié
    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
    }

    return response()->json(['message' => 'Email vérifié avec succès !'], 200);
}
    
}

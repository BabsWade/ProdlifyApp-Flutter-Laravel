<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Events\Verified;

class AuthController extends Controller
{
    // Inscription d'un nouvel utilisateur
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $role = $request->role ?? 'admin'; // Défaut = user
        $user->assignRole($role);
    
        // Envoi de l'email de vérification
        $user->notify(new VerifyEmailNotification());

        return response()->json([
            'message' => 'Utilisateur créé avec succès avec le rôle ' . $role,
        ], 201);

        return response()->json([
            'message' => 'Utilisateur créé avec succès. Veuillez vérifier votre email.',
        ], 201);
    }

    // Connexion d'un utilisateur
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // Vérification des identifiants de l'utilisateur
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);

            // Rediriger vers la liste des produits après la connexion réussie
            return response()->json([
                'message' => 'Connexion réussie',
                'token' => $token,
                'redirect_to' => url('/api/products'),  // Rediriger vers la liste des produits
            ]);
        }

        return response()->json(['error' => 'Identifiants invalides'], 401);
    }

    // Rafraîchir le token JWT
    public function refresh(Request $request)
    {
        $token = JWTAuth::getToken();
        $newToken = JWTAuth::refresh($token);

        return response()->json([
            'token' => $newToken,
        ]);
    }

    // Déconnexion de l'utilisateur (invalidation du token)
    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Déconnexion réussie']);
    }

    // Vérification de l'email de l'utilisateur
    public function verifyEmail($id)
    {
        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return response()->json(['message' => 'Email vérifié avec succès']);
    }
}
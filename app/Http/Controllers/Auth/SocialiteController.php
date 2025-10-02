<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToProvider($provider)
    {
        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            \Log::error('OAuth Redirect Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Authentication setup failed: ' . $e->getMessage());
        }
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Check if user already exists
            $user = User::where('email', $socialUser->getEmail())->first();
            
            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'email_verified_at' => now(),
                ]);
            }
            
            // Log the user in
            Auth::login($user, true);
            
            return redirect()->intended('/admin');
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('OAuth Error: ' . $e->getMessage());
            \Log::error('OAuth Error Details: ' . $e->getTraceAsString());
            
            return redirect('/login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }
}

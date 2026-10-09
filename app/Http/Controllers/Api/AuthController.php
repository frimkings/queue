<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Authenticate staff user.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::with(['department', 'doctorStation', 'counter'])
            ->where('email', $validated['email'])
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password credentials.',
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your staff account is currently suspended. Please contact the administrator.',
            ], 403);
        }

        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => "Welcome back, {$user->name}",
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'department' => $user->department?->name,
                    'doctor_station' => $user->doctorStation ? [
                        'id' => $user->doctorStation->id,
                        'name' => $user->doctorStation->name,
                        'room' => $user->doctorStation->room,
                    ] : null,
                    'counter' => $user->counter?->name,
                ],
            ],
        ]);
    }

    /**
     * Log out the current user session.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Get currently authenticated user details.
     */
    public function me(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'authenticated' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'authenticated' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'department' => $user->department?->name,
                    'doctor_station' => $user->doctorStation ? [
                        'id' => $user->doctorStation->id,
                        'name' => $user->doctorStation->name,
                        'room' => $user->doctorStation->room,
                    ] : null,
                    'counter' => $user->counter?->name,
                ],
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MobileAuthController extends Controller
{
    /**
     * Create a new account. Rejects if the phone number is already registered —
     * returning users must use login() instead, proving ownership via PIN.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'pin' => ['required', 'digits:6'],
        ]);

        $name = strip_tags($request->input('name'));
        $phone = strip_tags($request->input('phone'));
        $pin = $request->input('pin');

        if (MobileUser::where('phone', $phone)->exists()) {
            return response()->json([
                'success' => false,
                'code' => 'PHONE_ALREADY_REGISTERED',
                'message' => 'Nomor HP ini sudah terdaftar. Gunakan menu Masuk dengan PIN Anda.',
            ], 409);
        }

        $user = new MobileUser();
        $user->id = Str::uuid()->toString();
        $user->name = $name;
        $user->phone = $phone;
        $user->pin = Hash::make($pin);

        $plainToken = Str::random(64);
        $user->auth_token = hash('sha256', $plainToken);
        $user->last_login_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'token' => $plainToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
            ],
        ]);
    }

    /**
     * Re-authenticate an existing account (reinstall / new device) via phone + PIN.
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:50'],
            'pin' => ['required', 'digits:6'],
        ]);

        $phone = strip_tags($request->input('phone'));
        $pin = $request->input('pin');

        $user = MobileUser::where('phone', $phone)->first();

        if (!$user || !Hash::check($pin, $user->pin)) {
            return response()->json([
                'success' => false,
                'code' => 'INVALID_CREDENTIALS',
                'message' => 'Nomor HP atau PIN salah.',
            ], 401);
        }

        $plainToken = Str::random(64);
        $user->auth_token = hash('sha256', $plainToken);
        $user->last_login_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'token' => $plainToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
            ],
        ]);
    }
}

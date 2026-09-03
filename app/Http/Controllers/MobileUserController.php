<?php

namespace App\Http\Controllers;

use App\Models\MobileUser;
use Illuminate\Http\Request;

class MobileUserController extends Controller
{
    public function index(Request $request)
    {
        $query = MobileUser::query()
            ->withCount(['sessions', 'events']);

        if ($request->filled('q')) {
            $search = (string) $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest('created_at')->paginate(20)->withQueryString();

        return view('mobile-users.index', compact('users'));
    }

    public function toggleActive(MobileUser $mobileUser)
    {
        $mobileUser->is_active = !$mobileUser->is_active;

        // Force logout immediately when deactivating — the current token stops working.
        if (!$mobileUser->is_active) {
            $mobileUser->auth_token = null;
        }

        $mobileUser->save();

        return back()->with(
            'success',
            $mobileUser->is_active
                ? "Akun \"{$mobileUser->name}\" diaktifkan kembali."
                : "Akun \"{$mobileUser->name}\" dinonaktifkan."
        );
    }
}

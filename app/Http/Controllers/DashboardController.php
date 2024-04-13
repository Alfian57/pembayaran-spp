<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.pages.dashboard.index', [
            'title' => 'Dashboard',
        ]);
    }

    public function profile()
    {
        return view('dashboard.pages.dashboard.profile', [
            'title' => 'Profil',
        ]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        /** @var \App\Models\Account $user * */
        $user = Auth::user();

        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            toast('Password telah diupdate', 'success');

            return redirect()->route('dashboard.index');
        }

        toast('Password lama salah', 'error');

        return redirect()->route('dashboard.profile');
    }
}

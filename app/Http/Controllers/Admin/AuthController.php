<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() {
        return view('admin.login');
    }

    public function login(Request $request) {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        $admin = Admin::where('email',$data['email'])->first();
        if (!$admin || !Hash::check($data['password'], $admin->password)) {
            return back()->withErrors(['email' => 'Invalid credentials']);
        }
        $request->session()->put('admin_id', $admin->id);
        return redirect()->route('admin.messages.index');
    }

    public function logout(Request $request) {
        $request->session()->forget('admin_id');
        return redirect()->route('admin.login');
    }
}

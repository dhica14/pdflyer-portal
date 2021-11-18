<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $req)
    {
        $req->session()->invalidate();
        $req->session()->regenerateToken();
        return redirect()->action([LoginController::class, 'login']);
    }
}

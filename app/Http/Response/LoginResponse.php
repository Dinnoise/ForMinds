<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();
        $redirect = $user && $user->role === 'admin'
            ? '/admin/dashboard'
            : '/'; // или route('home')
        return redirect()->intended($redirect);
    }
}

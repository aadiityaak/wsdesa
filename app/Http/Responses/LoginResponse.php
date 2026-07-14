<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if (in_array($user->role, ['admin', 'super_admin', 'operator'])) {
            return redirect('/admin');
        }

        return redirect(Fortify::home());
    }
}

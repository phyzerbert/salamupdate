<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class PasswordExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        $password_updated_at = new Carbon(($user->password_updated_at) ? $user->password_updated_at : $user->created_at);

        if (Carbon::now()->diffInDays($password_updated_at) >= 30) {
            return redirect()->route('password.expired');
        }
        return $next($request);
    }
}

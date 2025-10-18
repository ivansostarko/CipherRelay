<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThreadSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('thread_id') || !$request->session()->has('thread_key')) {
            return redirect()->route('thread.enter')->with('error', 'Please enter your passcode.');
        }
        return $next($request);
    }
}

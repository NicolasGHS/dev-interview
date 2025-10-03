<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = ['en', 'fr', 'nl'];
        
        // Check for locale in request parameter (for switching)
        if ($request->has('locale') && in_array($request->get('locale'), $availableLocales)) {
            $locale = $request->get('locale');
            Session::put('locale', $locale);
        } 
        // Otherwise use session locale if available
        elseif (Session::has('locale') && in_array(Session::get('locale'), $availableLocales)) {
            $locale = Session::get('locale');
        }
        // Fall back to browser preference or default
        else {
            $locale = $request->getPreferredLanguage($availableLocales) ?: config('app.locale');
        }

        App::setLocale($locale);
        
        return $next($request);
    }
}

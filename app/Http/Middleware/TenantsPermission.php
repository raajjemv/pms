<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantsPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!empty(auth()->user())) {
            $tenant = auth()->user()->tenant;
            setPermissionsTeamId($tenant);
            auth()->user()->update([
                'current_tenant_id' => Filament::getTenant()->id
            ]);
        }
        return $next($request);
    }
}

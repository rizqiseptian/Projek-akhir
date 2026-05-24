<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Database\Eloquent\Model;

class AuthenticateFilamentEmployee extends Middleware
{
    /**
     * Replicate Filament's Authenticate middleware but redirect
     * unauthenticated users to our custom employee login page.
     *
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);
            return; /** @phpstan-ignore-line */
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user  = $guard->user();

        // Restrict admin panel paths to administrators only
        if ($request->is('admin*')) {
            abort_unless($user && $user->is_admin, 403);
        }

        $panel = Filament::getCurrentOrDefaultPanel();

        // If the user implements FilamentUser, defer to canAccessPanel().
        // For our Employee model (which does not implement FilamentUser) we
        // rely solely on the is_admin gate above, so we skip the abort here.
        if ($user instanceof FilamentUser && ! $user->canAccessPanel($panel)) {
            abort(403);
        }
    }

    /**
     * Always redirect back to our employee login page.
     */
    protected function redirectTo($request): ?string
    {
        // If the requested path is for the admin panel, send users
        // to the admin-specific login page so the frontend can set
        // the correct `redirectTo` target (e.g. /admin).
        if ($request->is('admin*')) {
            return route('admin.login');
        }

        return route('employee.login');
    }
}

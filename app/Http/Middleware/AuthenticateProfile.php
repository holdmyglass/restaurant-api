<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\User\Services\V1\ProfileService;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateProfile
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if ($token) {
            $profile = $this->profileService->getAuthenticatedProfileFromToken($token);

            if ($profile) {
                $request->merge(['profile' => $profile]);
            }
        }

        return $next($request);
    }
}

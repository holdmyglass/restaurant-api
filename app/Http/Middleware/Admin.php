<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Modules\Shared\Enums\ServerStatusCodeEnum;
use Modules\User\Enums\UserLevelEnum;
use Modules\User\Services\V1\ProfileService;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    public function __construct(
        private readonly ProfileService $profileService,
        private readonly Guard $auth

    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! ($token = $request->bearerToken())) {
            return response()->json(
                [
                    'message' => 'Unauthorized',
                ], ServerStatusCodeEnum::UNAUTHORIZED->value);
        }

        if (! ($profile = $this->profileService->getAuthenticatedProfileFromToken($token))) {
            return response()->json(
                [
                    'message' => 'Unauthorized',
                ], ServerStatusCodeEnum::UNAUTHORIZED->value);
        }

        if (! $this->auth->check() || ! in_array($profile->level, [UserLevelEnum::ADMIN, UserLevelEnum::SUPERADMIN])) {
            return response()->json([
                'error' => 'Unauthorized',
            ], ServerStatusCodeEnum::UNAUTHORIZED->value);
        }

        return $next($request);
    }
}

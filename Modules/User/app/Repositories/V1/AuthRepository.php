<?php

namespace Modules\User\Repositories\V1;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Modules\Shared\Services\V1\SystemService;
use Modules\User\DTO\V1\UserDTO;
use Modules\User\Interfaces\V1\AuthRepositoryInterface;
use Modules\User\Models\User;

class AuthRepository implements AuthRepositoryInterface
{
    public function __construct(
        private readonly SystemService $systemService
    ) {}

    public function createUser(UserDTO $userDTO): User
    {
        $user = new User;
        $user->email = $userDTO->email ?? $user->email;
        $user->phone = $userDTO->phone ?? $userDTO->phone;
        $user->password = Hash::make($userDTO->password);
        $user->save();

        return $user;
    }

    public function verifyEmail(User $user): User
    {

        // verify email
        $user->email_verified_at = Carbon::now();
        $user->save();

        return $user;
    }

    public function verifyPhone(User $user): User
    {
        // verify email
        $user->phone_verified_at = Carbon::now();
        $user->save();

        return $user;
    }
}

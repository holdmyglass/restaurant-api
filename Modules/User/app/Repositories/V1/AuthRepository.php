<?php

namespace Modules\User\Repositories\V1;

use Illuminate\Support\Facades\Hash;
use Modules\User\DTO\V1\UserDTO;
use Modules\User\Interfaces\V1\AuthRepositoryInterface;
use Modules\User\Models\User;

class AuthRepository implements AuthRepositoryInterface
{
    public function createUser(UserDTO $userDTO): User
    {

        $user = new User;
        $user->email = $userDTO->email ?? $user->email;
        $user->phone = $userDTO->phone ?? $userDTO->phone;
        $user->password = Hash::make($userDTO->password);
        $user->save();

        return $user;
    }
}

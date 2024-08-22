<?php

namespace Modules\User\Interfaces\V1;

use Modules\User\DTO\V1\UserDTO;
use Modules\User\Models\User;

interface AuthRepositoryInterface
{
    public function createuser(UserDTO $userDTO): User;

    public function verifyEmail(User $user): User;

    public function verifyPhone(User $user): User;
}

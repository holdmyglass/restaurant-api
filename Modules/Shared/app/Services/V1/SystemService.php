<?php

namespace Modules\Shared\Services\V1;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\User\Enums\UserLevelEnum;
use Modules\User\Models\User;

class SystemService
{
    public function createSystem(): User
    {

        $system = User::firstWhere('level', UserLevelEnum::SYSTEM->value);

        if (! $system) {

            $password = env('SYSTEM_PASSWORD', 'qh(!{Q28Zn}rx&$:Z/SpcQV&');
            $hashedPassword = Hash::make($password);

            $system = User::create([
                'uuid' => Str::uuid(),
                'password' => $hashedPassword,
                'level' => UserLevelEnum::SYSTEM->value,
            ]);
        }

        return $system;
    }

    public function getSystem(): User|false
    {
        $system = User::where('level', UserLevelEnum::SYSTEM->value)->first();

        if (! $system) {
            $this->createSystem();
        }

        $system = User::where('level', UserLevelEnum::SYSTEM->value)->first();

        return $system ?: false;
    }

    public function getSystemId(): string|false
    {
        $system = $this->getSystem();

        return $system->id;
    }
}

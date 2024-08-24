<?php

namespace Modules\User\Services\V1;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Laravel\Passport\PersonalAccessTokenResult;
use Modules\User\DTO\V1\ProfileDTO;
use Modules\User\DTO\V1\ProfileImageDTO;
use Modules\User\DTO\V1\ProfileTokenDTO;
use Modules\User\Interfaces\V1\ProfileRepositoryInterface;
use Modules\User\Interfaces\V1\ProfileTokenRepositoryInterface;
use Modules\User\Models\Profile;
use Modules\User\Models\ProfileToken;
use Modules\User\Models\User;

class ProfileService
{
    public function __construct(
        private readonly ProfileRepositoryInterface $profileRepository,
        private readonly ProfileTokenRepositoryInterface $profileTokenRepository
    ) {}

    public function createProfile(ProfileDTO $profileDto): Profile
    {

        $profile = $this->profileRepository->createProfile($profileDto);

        return $profile;
    }

    public function generateUsername(string $username = ''): string
    {
        if (is_null($username) || empty($username)) {
            $username = Str::random(13);
        }

        while (Profile::where('user_name', '=', $username)->exists()) {
            $username = Str::random(13);
        }

        return $username;
    }

    public function saveDisplayImage(mixed $file, Profile $profile): Profile
    {
        do {
            $filename = str::random(40).'.'.$file->getClientOriginalExtension();
            $originalPath = "dp/original/$filename";
            $thumbnailPath = "dp/thumbnail/$filename";
        } while (
            // Check if the filename already exists in the database
            Profile::where('display_image', $filename)->exists() ||
            // Check if the file already exists in the location
            Storage::disk('public')->exists($originalPath) ||
            Storage::disk('public')->exists($thumbnailPath)
        );

        Storage::disk('public')->put($originalPath, file_get_contents($file));

        $thumbnail = Image::make($file)
            ->fit(200, 200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());

        $profileImageDto = new ProfileImageDTO(
            profileId: $profile->id,
            displayImg: $filename,
            coverImg: null
        );

        $this->profileRepository->updateDisplayImage($profile, $profileImageDto);

        return $profile;
    }

    public function saveCoverImage(mixed $file, Profile $profile): Profile
    {
        do {
            $filename = Str::random(40).'.'.$file->getClientOriginalExtension();
            $originalPath = "cover/original/$filename";
            $thumbnailPath = "cover/thumbnail/$filename";
        } while (
            // Check if the filename already exists in the database
            Profile::where('cover_image', $filename)->exists() ||
            // Check if the file already exists in the location
            Storage::disk('public')->exists($originalPath) ||
            Storage::disk('public')->exists($thumbnailPath)
        );

        Storage::disk('public')->put($originalPath, file_get_contents($file));

        $thumbnail = Image::make($file)
            ->fit(200, 200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());

        $profileImageDto = new ProfileImageDTO(
            profileId: $profile->id,
            displayImg: null,
            coverImg: $filename
        );

        $this->profileRepository->updateCoverImage($profile, $profileImageDto);

        return $profile;
    }

    public function assignTokenToUserAndProfile(User $user, ?Profile $profile = null): PersonalAccessTokenResult
    {
        if (! $profile) {
            $profile = $user->getUserDefaultProfile();
        }

        // Create a new personal access token for the user
        $tokenResult = $user->createToken('ProfileToken');

        $profileTokenDto = new ProfileTokenDTO(
            profileId: $profile->id,
            accessToken: $tokenResult->accessToken
        );

        $this->profileTokenRepository->createProfileToken($profileTokenDto);

        return $tokenResult;
    }

    public function getAuthenticatedProfileFromToken($token): ?Profile
    {
        $tokenRecord = ProfileToken::where('access_token', $token)->first();

        if ($tokenRecord) {
            return $tokenRecord->profile;
        }

        return null;
    }
}

<?php

namespace Modules\File\Services\V1;

use Illuminate\Support\Facades\Storage;
use Modules\File\Enums\ImageSizeEnum;

class ImageService
{
    public function getImageUrl(ImageSizeEnum $size, string $filename): string
    {
        $location = ImageSizeEnum::from($size->value)->getLocation();
        $filePath = "images/$location/$filename";

        $imageUrl = Storage::url($filePath);
        $baseUrl = config('app.url');

        return "{$baseUrl}{$imageUrl}";

        // $imageUrl = Storage::disk('s3')->url($filePath);
    }
}

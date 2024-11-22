<?php

namespace Modules\File\Enums;

enum ImageFormatEnum: string
{
    case ORIGINAL = 'original';
    case JPG = 'jpg';
    case JPEG = 'jpeg';
    case PNG = 'png';
    case GIF = 'gif';
    case BMP = 'bmp';
    case WEBP = 'webp';

    public static function getAllFormats(): array
    {
        return array_column(self::cases(), 'value');
    }
}

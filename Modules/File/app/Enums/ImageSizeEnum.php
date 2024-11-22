<?php

namespace Modules\File\Enums;

enum ImageSizeEnum: string
{
    case ORIGINAL = 'ORIGINAL';
    case AVATAR = 'AVATAR';
    case THUMBNAIL = 'THUMBNAIL';
    case SMALLVERTICAL = 'SMALLSMALLVERTICAL';
    case SMALLHORIZONTAL = 'SMALLSMALLHORIZONTAL';
    case SMALLSQUARE = 'SMALLSQUARE';
    case MEDIUMVERTICAL = 'MEDIUMVERTICAL';
    case MEDIUMHORIZONTAL = 'MEDIUMHORIZONTAL';
    case MEDIUMSQUARE = 'MEDIUMSQUARE';

    public static function isValid(string $value): bool
    {
        // Check if the value matches any of the enum cases
        return in_array($value, array_column(self::cases(), 'value'), true);
    }

    public function getResolution(): array
    {
        return match ($this) {
            self::ORIGINAL => [],
            self::AVATAR => [75, 75],
            self::THUMBNAIL => [150, 150],   // Example resolution for THUMBNAIL
            self::SMALLVERTICAL => [300, 400],
            self::SMALLHORIZONTAL => [400, 300],
            self::SMALLSQUARE => [300, 300],       // Example resolution for SMALL
            self::MEDIUMVERTICAL => [600, 800],
            self::MEDIUMHORIZONTAL => [800, 600],
            self::MEDIUMSQUARE => [600, 600],      // Example resolution for M
        };
    }

    public function getLocation(): string
    {
        return match ($this) {
            self::AVATAR => 'f8g3h2j4k5l1m',
            self::THUMBNAIL => 'z1x0c2v3b4n5m',
            self::SMALLVERTICAL => 'q2w4e6r8t0y1u',
            self::SMALLHORIZONTAL => 'a7b2c4d9e5f1g',
            self::SMALLSQUARE => 'a1b3c5d7e9f0g',
            self::MEDIUMVERTICAL => 'm5n7p1q3r8s2a',
            self::MEDIUMHORIZONTAL => 'h9j0k8l6m4n3o',
            self::MEDIUMSQUARE => 'c4v2b1n9m8l7k',
            self::ORIGINAL => 'j3k5l7m2n8o0p',
        };
    }
}

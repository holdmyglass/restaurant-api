<?php

namespace Modules\Shared\Services\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class HelperService
{
    public static function Slugify(string $slug, string $field, Model $model, ?string $versionIdentifier = null): string
    {
        if ($slug) {
            $baseSlug = Str::slug($slug, '-');

            $query = $model->where($field, $baseSlug);

            if ($versionIdentifier) {
                $query->where('version_identifier', '!=', $versionIdentifier);
            }

            if ($query->exists()) {
                $slug = "$baseSlug-".Str::random(8);

                return static::Slugify($slug, $field, $model, $versionIdentifier);
            }

            return $baseSlug;
        }

        return Str::slug(Str::random(8), '-');
    }

    public static function rankify(Model $model, ?int $desiredRank = null): int
    {
        $isVersionable = $model->hasVersionableTrait();

        // Retrieve the maximum rank for the model's type, considering version if applicable
        $maxRank = $model::where($isVersionable ? 'version_identifier' : 'id', $model->{$isVersionable ? 'version_identifier' : 'id'})
            ->max('rank');

        // If desired rank is provided and valid, use it
        $rank = $desiredRank !== null && $desiredRank >= 0 && $desiredRank > $maxRank
        ? $desiredRank
        : $maxRank + 1;

        return $rank;
    }

    public static function isColumnNullable($table, $field)
    {
        return Schema::hasColumn($table, $field) && Schema::getColumnType($table, $field) === 'nullable';
    }

    //TODO  This method should be moved somewhere and the values should be fetched from database
    public static function getSupportedLanguages(): array
    {
        return [
            'en',
            'nl',
        ];
    }

    public static function getFallbackSlugAttributeFromTranslatable(array $attribute): string
    {
        $defaultLanguage = self::getDefaultLanguage();

        // Check if the 'en' attribute is present
        if (isset($attribute['en'])) {
            return $attribute['en'];
        }

        // If not, fall back to the default language
        return $attribute[$defaultLanguage] ?? '';
    }

    //TODO This method should be moved somewhere and the value should be fetched from database
    public static function getDefaultLanguage(): string
    {
        return 'nl';
    }
}

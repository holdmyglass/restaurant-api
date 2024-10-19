<?php

namespace Modules\Shared\Services\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
        $maxRank = $model::max('rank');

        // If desired rank is provided and valid, use it
        $rank = $desiredRank !== null && $desiredRank >= 0
        ? $desiredRank
        : $maxRank + 1;

        return $rank;
    }

    public static function isColumnNullable($table, $field)
    {
        // Check if the column exists
        if (! Schema::hasColumn($table, $field)) {
            return false; // Column does not exist, so it can't be nullable
        }

        // Get the column details using a raw query
        $columnDetails = DB::select("SELECT is_nullable FROM information_schema.columns WHERE table_name = '$table' AND column_name = '$field'");

        // Check if the column is nullable
        return $columnDetails[0]->is_nullable === 'YES';
    }

    public static function getValueFromNullableCheck(
        object|array $request,
        ?Model $model,
        string $columnName,
        bool $isUpdate,
        mixed $defaultValue = null
    ): mixed {
        if ($request->$columnName !== null) {
            return $request->$columnName;
        } elseif ($isUpdate && $model !== null && ! self::isColumnNullable($model->getTable(), $columnName)) {
            return $model->$columnName;
        } else {
            return $defaultValue;
        }
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

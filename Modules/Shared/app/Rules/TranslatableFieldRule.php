<?php

namespace Modules\Shared\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;
use Modules\Shared\Services\V1\HelperService;

class TranslatableFieldRule implements ValidationRule
{
    public function __construct(
        private readonly string $mode,
        private readonly int $minCount = 0,
        private readonly array $requiredLocales = [],
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $languages = HelperService::getSupportedLanguages();
        $languageKeys = array_keys($value);

        switch ($this->mode) {
            case 'all':
                $missingLanguages = array_diff($languages, $languageKeys);

                if (! empty($missingLanguages)) {
                    $fail("{$attribute} must be present for all languages: [".implode(', ', $languages).']');

                    return;
                }
                break;
            case 'none':
                if (! empty($languageKeys)) {
                    $fail("{$attribute} must not be present for any language.");

                    return;
                }
                break;
            case 'atLeast':
                if (count($languageKeys) < $this->minCount) {
                    $fail("{$attribute} must be present for at least {$this->minCount} languages.");

                    return;
                }
                break;
            case 'noneOrAll':
                if (! empty($languageKeys) && count($languageKeys) !== count($languages)) {
                    $fail("{$attribute} must be either present for all languages or not present for any language.");

                    return;
                }
                break;
            case 'atLeastLocales':
                $requiredLocales = $this->requiredLocales;
                $localeKeys = array_keys($value);
                $missingLocales = array_diff($requiredLocales, $localeKeys);

                if (! empty($missingLocales)) {
                    $fail("{$attribute} must be present for at least the following locales: ".implode(', ', $missingLocales));

                    return;
                }
                break;
            default:
                throw new InvalidArgumentException("Invalid mode: {$this->mode}");
        }
    }
}

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
        $filteredValue = array_filter($value, function ($val) {
            return ! is_null($val) && trim($val) !== '';
        });

        $languages = HelperService::getSupportedLanguages();
        $languageKeys = array_keys($filteredValue);

        switch ($this->mode) {
            case 'all':
                $missingLanguages = array_diff($languages, $languageKeys);
                if (! empty($missingLanguages)) {
                    $fail(__('shared::messages.rule.translatable_field_all', [
                        'attribute' => $attribute,
                        'languages' => implode(', ', $languages),
                    ]));
                }
                break;

            case 'none':
                if (! empty($languageKeys)) {
                    $fail(__('shared::messages.rule.translatable_field_none', [
                        'attribute' => $attribute,
                    ]));
                }
                break;

            case 'atLeast':
                if (count($languageKeys) < $this->minCount) {
                    $fail(__('shared::messages.rule.translatable_field_at_least', [
                        'attribute' => $attribute,
                        'minCount' => $this->minCount,
                    ]));
                }
                break;

            case 'noneOrAll':
                if (! empty($languageKeys) && count($languageKeys) !== count($languages)) {
                    $fail(__('shared::messages.rule.translatable_field_none_or_all', [
                        'attribute' => $attribute,
                    ]));
                }
                break;

            case 'atLeastLocales':
                $requiredLocales = $this->requiredLocales;
                $localeKeys = array_keys($value);
                $missingLocales = array_diff($requiredLocales, $localeKeys);
                if (! empty($missingLocales)) {
                    $fail(__('shared::messages.rule.translatable_field_at_least_locales', [
                        'attribute' => $attribute,
                        'locales' => implode(', ', $missingLocales),
                    ]));
                }
                break;

            default:
                throw new InvalidArgumentException(__('shared::messages.rule.invalid_mode', [
                    'mode' => $this->mode,
                ]));
        }
    }
}

<?php

namespace Modules\Shared\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MultiplePriceRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $uniqueCombinations = [];
        foreach ($value as $price) {
            $combination = implode('-', [$price['currency'], $price['price_type']]);
            if (in_array($combination, $uniqueCombinations)) {
                $fail(__('product::messages.rule.price_duplicate_combination'));
            }
            $uniqueCombinations[] = $combination;
        }
    }
}

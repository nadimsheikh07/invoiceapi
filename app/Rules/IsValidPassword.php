<?php

namespace App\Rules;

use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Rule;

class IsValidPassword implements Rule
{

    public $lengthPasses = true;
    public $uppercasePasses = true;
    public $numericPasses = true;
    public $specialCharacterPasses = true;
    public $min = 8;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->lengthPasses = (Str::length($value) >= $this->min);
        $this->uppercasePasses = (Str::lower($value) !== $value);
        $this->numericPasses = ((bool) preg_match('/[0-9]/', $value));
        $this->specialCharacterPasses = ((bool) preg_match('/[^A-Za-z0-9]/', $value));

        return ($this->lengthPasses && $this->uppercasePasses && $this->numericPasses && $this->specialCharacterPasses);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        switch (true) {
            case !$this->uppercasePasses
                && $this->numericPasses
                && $this->specialCharacterPasses:
                return __('passwordRule.uppercase', ["min" => $this->min]);

            case !$this->numericPasses
                && $this->uppercasePasses
                && $this->specialCharacterPasses:
                return __('passwordRule.number', ["min" => $this->min]);

            case !$this->specialCharacterPasses
                && $this->uppercasePasses
                && $this->numericPasses:
                return __('passwordRule.special', ["min" => $this->min]);

            case !$this->uppercasePasses
                && !$this->numericPasses
                && $this->specialCharacterPasses:
                return __('passwordRule.uppercaseNumber', ["min" => $this->min]);

            case !$this->uppercasePasses
                && !$this->specialCharacterPasses
                && $this->numericPasses:
                return __('passwordRule.uppercaseSpecial', ["min" => $this->min]);

            case !$this->uppercasePasses
                && !$this->numericPasses
                && !$this->specialCharacterPasses:
                return __('passwordRule.uppercaseSpecialNumber', ["min" => $this->min]);

            default:
                return __('passwordRule.default', ['min' => $this->min]);
        }
    }
}

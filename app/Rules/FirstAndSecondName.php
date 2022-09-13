<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FirstAndSecondName implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return preg_match("/^[A-Z][a-z]+\s[A-Z][a-z]+$/", $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return "The :attribute must be starts with uppercase, has a single space between first and second name";
    }
}

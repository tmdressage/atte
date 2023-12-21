<?php

namespace App\Actions\Fortify;

use App\Actions\Fortify\Password;
trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function passwordRules(): array
    {
        return ['required', new Password, 'confirmed', 'between:8,191'];
    }

    
}


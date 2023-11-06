<?php

namespace App\Rules;

use App\Models\Company;
use Closure;
use Illuminate\Contracts\Validation\Rule as ValadationRule;

use function Deployer\fail;

class UniqueRucRule implements ValadationRule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public $user_id;


    public function __construct($user_id)
    {
        $this->user_id = $user_id;
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

        $company = \App\Models\Company::where('ruc', $value)
                    ->where('user_id', $this->user_id)->first();
        if ($company) {
            ('Ya existe una empresa con este RUC');
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El mensaje de error de validación.';
    }
}

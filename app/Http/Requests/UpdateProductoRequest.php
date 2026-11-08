<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateProductoRequest extends StoreProductoRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'quitar_imagen' => ['nullable', 'boolean'],
            'quitar_modelo' => ['nullable', 'boolean'],
        ];
    }
}

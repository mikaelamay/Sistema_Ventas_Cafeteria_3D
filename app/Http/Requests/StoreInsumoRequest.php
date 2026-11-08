<?php

namespace App\Http\Requests;

use App\Models\Insumo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInsumoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'unidad_medida' => ['required', 'string', 'max:50'],
            'stock_actual' => ['required', 'numeric', 'min:0'],
            'stock_minimo' => ['required', 'numeric', 'min:0'],
            'costo_unitario' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del insumo es obligatorio.',
            'stock_actual.min' => 'El stock actual no puede ser negativo.',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo.',
        ];
    }

    /**
     * Normalize boolean and numeric fields before validation.
     *
     * @return void
     */

    /**
     * Persist a new supply.
     */
    public function persist(): Insumo
    {
        return Insumo::create($this->validated());
    }
}

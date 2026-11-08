<?php

namespace App\Http\Requests;

use App\Models\Insumo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMovimientoRequest extends FormRequest
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
            'tipo' => ['required', 'string', 'in:entrada,salida,ajuste'],
            'cantidad' => ['required', 'numeric', 'min:0.001'],
            'motivo' => ['nullable', 'string', 'max:255'],
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
            'tipo.required' => 'Debe seleccionar el tipo de movimiento.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.min' => 'La cantidad debe ser mayor que cero.',
        ];
    }

    /**
     * Normalize numeric fields before validation.
     *
     * @return void
     */

    /**
     * Get the supply being moved.
     */
    public function insumo(): Insumo
    {
        return Insumo::findOrFail($this->route('insumo'));
    }
}

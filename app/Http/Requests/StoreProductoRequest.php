<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Normalize the checkbox that marks a product as active.
     */
    protected function prepareForValidation(): void
    {
        if ($this->exists('activo')) {
            $this->merge([
                'activo' => $this->boolean('activo'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * The 3D model rule relies on the `extensions` rule because browsers
     * commonly send `.glb` files as `application/octet-stream`; the file
     * extension is therefore the only reliable discriminator. The image
     * rule validates both the extension and the real MIME type.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'categoria' => ['nullable', 'string', 'max:255'],
            'activo' => ['sometimes', 'boolean'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'modelo_3d' => ['nullable', 'file', 'extensions:glb,gltf', 'max:20480'],
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
            'imagen.image' => 'La imagen debe ser un archivo de imagen válido.',
            'imagen.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'imagen.max' => 'La imagen no puede superar los 4 MB.',
            'modelo_3d.extensions' => 'El modelo 3D debe ser un archivo .glb o .gltf.',
            'modelo_3d.max' => 'El modelo 3D no puede superar los 20 MB.',
        ];
    }
}

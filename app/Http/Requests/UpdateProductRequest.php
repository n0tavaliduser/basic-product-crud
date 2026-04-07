<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        $presenceRule = $this->isMethod('patch') ? 'sometimes' : 'required';

        return [
            'name' => [$presenceRule, 'string', 'max:255'],
            'price' => [$presenceRule, 'numeric', 'min:0'],
            'stock' => [$presenceRule, 'integer', 'min:0'],
            'description' => [$presenceRule, 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama produk',
            'price' => 'harga',
            'stock' => 'stok',
            'description' => 'deskripsi',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'harga' => 'required|numeric|min:0|decimal:0,2',
            'stok' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul buku wajib diisi.',
            'judul.string' => 'Judul buku harus berupa teks.',
            'judul.max' => 'Judul buku tidak boleh lebih dari 255 karakter.',
            'pengarang.required' => 'Nama pengarang wajib diisi.',
            'pengarang.string' => 'Nama pengarang harus berupa teks.',
            'pengarang.max' => 'Nama pengarang tidak boleh lebih dari 255 karakter.',
            'penerbit.string' => 'Nama penerbit harus berupa teks.',
            'penerbit.max' => 'Nama penerbit tidak boleh lebih dari 255 karakter.',
            'harga.required' => 'Harga buku wajib diisi.',
            'harga.numeric' => 'Harga buku harus berupa angka.',
            'harga.min' => 'Harga buku tidak boleh kurang dari 0.',
            'harga.decimal' => 'Harga buku harus dalam format desimal yang valid.',
            'stok.integer' => 'Stok buku harus berupa angka bulat.',
            'stok.min' => 'Stok buku tidak boleh kurang dari 0.',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'judul' => 'judul buku',
            'pengarang' => 'pengarang',
            'penerbit' => 'penerbit',
            'harga' => 'harga',
            'stok' => 'stok',
        ];
    }
}
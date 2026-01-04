<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CashierRequest extends FormRequest
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
        $cashierId = $this->route('cashier') ? $this->route('cashier')->id : null;

        return [
            'nama' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('kasir', 'email')->ignore($cashierId)
            ],
            'no_karyawan' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kasir', 'no_karyawan')->ignore($cashierId)
            ],
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
            'nama.required' => 'Nama kasir wajib diisi.',
            'nama.string' => 'Nama kasir harus berupa teks.',
            'nama.max' => 'Nama kasir tidak boleh lebih dari 255 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'email.unique' => 'Email sudah digunakan oleh kasir lain.',
            'no_karyawan.required' => 'Nomor karyawan wajib diisi.',
            'no_karyawan.string' => 'Nomor karyawan harus berupa teks.',
            'no_karyawan.max' => 'Nomor karyawan tidak boleh lebih dari 50 karakter.',
            'no_karyawan.unique' => 'Nomor karyawan sudah digunakan oleh kasir lain.',
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
            'nama' => 'nama kasir',
            'email' => 'email',
            'no_karyawan' => 'nomor karyawan',
        ];
    }
}
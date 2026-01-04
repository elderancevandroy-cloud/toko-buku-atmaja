<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
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
            'kasir_id' => 'required|exists:kasir,id',
            'total_harga' => 'required|numeric|min:0|decimal:0,2',
            'tanggal_penjualan' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.buku_id' => 'required|exists:buku,id',
            'details.*.jumlah' => 'required|integer|min:1',
            'details.*.harga_satuan' => 'required|numeric|min:0|decimal:0,2',
            'details.*.subtotal' => 'required|numeric|min:0|decimal:0,2',
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
            'kasir_id.required' => 'Kasir wajib dipilih.',
            'kasir_id.exists' => 'Kasir yang dipilih tidak valid.',
            'total_harga.required' => 'Total harga wajib diisi.',
            'total_harga.numeric' => 'Total harga harus berupa angka.',
            'total_harga.min' => 'Total harga tidak boleh kurang dari 0.',
            'total_harga.decimal' => 'Total harga harus dalam format desimal yang valid.',
            'tanggal_penjualan.required' => 'Tanggal penjualan wajib diisi.',
            'tanggal_penjualan.date' => 'Format tanggal penjualan tidak valid.',
            'details.required' => 'Detail penjualan wajib diisi.',
            'details.array' => 'Detail penjualan harus berupa array.',
            'details.min' => 'Minimal harus ada 1 item dalam penjualan.',
            'details.*.buku_id.required' => 'Buku wajib dipilih untuk setiap item.',
            'details.*.buku_id.exists' => 'Buku yang dipilih tidak valid.',
            'details.*.jumlah.required' => 'Jumlah wajib diisi untuk setiap item.',
            'details.*.jumlah.integer' => 'Jumlah harus berupa angka bulat.',
            'details.*.jumlah.min' => 'Jumlah minimal 1 untuk setiap item.',
            'details.*.harga_satuan.required' => 'Harga satuan wajib diisi untuk setiap item.',
            'details.*.harga_satuan.numeric' => 'Harga satuan harus berupa angka.',
            'details.*.harga_satuan.min' => 'Harga satuan tidak boleh kurang dari 0.',
            'details.*.harga_satuan.decimal' => 'Harga satuan harus dalam format desimal yang valid.',
            'details.*.subtotal.required' => 'Subtotal wajib diisi untuk setiap item.',
            'details.*.subtotal.numeric' => 'Subtotal harus berupa angka.',
            'details.*.subtotal.min' => 'Subtotal tidak boleh kurang dari 0.',
            'details.*.subtotal.decimal' => 'Subtotal harus dalam format desimal yang valid.',
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
            'kasir_id' => 'kasir',
            'total_harga' => 'total harga',
            'tanggal_penjualan' => 'tanggal penjualan',
            'details' => 'detail penjualan',
            'details.*.buku_id' => 'buku',
            'details.*.jumlah' => 'jumlah',
            'details.*.harga_satuan' => 'harga satuan',
            'details.*.subtotal' => 'subtotal',
        ];
    }
}
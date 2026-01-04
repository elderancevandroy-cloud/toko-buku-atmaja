<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'distributor_id' => 'required|exists:distributor,id',
            'buku_id' => 'required|exists:buku,id',
            'jumlah' => 'required|integer|min:1',
            'harga_beli' => 'required|numeric|min:0|decimal:0,2',
            'total' => 'required|numeric|min:0|decimal:0,2',
            'tanggal_pembelian' => 'required|date',
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
            'distributor_id.required' => 'Distributor wajib dipilih.',
            'distributor_id.exists' => 'Distributor yang dipilih tidak valid.',
            'buku_id.required' => 'Buku wajib dipilih.',
            'buku_id.exists' => 'Buku yang dipilih tidak valid.',
            'jumlah.required' => 'Jumlah pembelian wajib diisi.',
            'jumlah.integer' => 'Jumlah pembelian harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah pembelian minimal 1.',
            'harga_beli.required' => 'Harga beli wajib diisi.',
            'harga_beli.numeric' => 'Harga beli harus berupa angka.',
            'harga_beli.min' => 'Harga beli tidak boleh kurang dari 0.',
            'harga_beli.decimal' => 'Harga beli harus dalam format desimal yang valid.',
            'total.required' => 'Total pembelian wajib diisi.',
            'total.numeric' => 'Total pembelian harus berupa angka.',
            'total.min' => 'Total pembelian tidak boleh kurang dari 0.',
            'total.decimal' => 'Total pembelian harus dalam format desimal yang valid.',
            'tanggal_pembelian.required' => 'Tanggal pembelian wajib diisi.',
            'tanggal_pembelian.date' => 'Format tanggal pembelian tidak valid.',
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
            'distributor_id' => 'distributor',
            'buku_id' => 'buku',
            'jumlah' => 'jumlah',
            'harga_beli' => 'harga beli',
            'total' => 'total',
            'tanggal_pembelian' => 'tanggal pembelian',
        ];
    }
}
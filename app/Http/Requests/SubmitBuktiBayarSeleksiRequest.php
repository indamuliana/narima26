<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitBuktiBayarSeleksiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isCalonSiswa() || auth()->user()->isAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bank_pengirim' => ['required', 'string', 'max:100'],
            'nama_pengirim' => ['required', 'string', 'max:150'],
            'nomor_referensi' => ['nullable', 'string', 'max:100'],
            'tanggal_bayar' => ['required', 'date', 'before_or_equal:today'],
            'nominal_dibayar' => ['required', 'numeric', 'min:10000'],
            'bukti_transfer' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bank_pengirim.required' => 'Nama bank atau kanal pembayaran pengirim wajib diisi.',
            'nama_pengirim.required' => 'Nama pemilik rekening pengirim wajib diisi.',
            'tanggal_bayar.required' => 'Tanggal pembayaran wajib diisi.',
            'tanggal_bayar.before_or_equal' => 'Tanggal transfer tidak boleh melebihi hari ini.',
            'nominal_dibayar.required' => 'Nominal yang Anda transfer wajib diisi.',
            'nominal_dibayar.numeric' => 'Nominal harus berupa angka.',
            'bukti_transfer.required' => 'File bukti transfer (struk ATM / tangkapan layar m-banking) wajib diunggah.',
            'bukti_transfer.mimes' => 'Bukti transfer harus berformat JPG, JPEG, PNG, atau PDF.',
            'bukti_transfer.max' => 'Ukuran file bukti transfer maksimal 10 MB.',
        ];
    }
}

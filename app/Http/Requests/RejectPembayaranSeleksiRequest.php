<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectPembayaranSeleksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isBendahara() || auth()->user()->isAdmin());
    }

    public function rules(): array
    {
        return [
            'alasan' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'alasan.required' => 'Alasan penolakan bukti pembayaran wajib diisi.',
            'alasan.min' => 'Alasan penolakan minimal 5 karakter.',
        ];
    }
}

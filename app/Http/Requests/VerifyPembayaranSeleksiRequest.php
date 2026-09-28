<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPembayaranSeleksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isBendahara() || auth()->user()->isAdmin());
    }

    public function rules(): array
    {
        return [
            'nominal_diterima' => ['nullable', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }
}

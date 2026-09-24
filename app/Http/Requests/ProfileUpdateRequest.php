<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'no_whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'nik' => ['nullable', 'digits:16', Rule::unique(User::class)->ignore($this->user()->id)],
            'no_kk' => ['nullable', 'digits:16'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'status_perkawinan' => ['nullable', 'in:belum_kawin,kawin,cerai_hidup,cerai_mati'],
            'agama' => ['nullable', 'string', 'max:20'],
            'pendidikan_terakhir' => ['nullable', 'string', 'max:30'],
            'pekerjaan' => ['nullable', 'string', 'max:50'],
            'alamat_rumah' => ['nullable', 'string', 'max:255'],
            'no_rumah' => ['nullable', 'string', 'max:10'],
        ];
    }
}

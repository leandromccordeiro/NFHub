<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNotaFiscalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Por enquanto permitindo acesso público
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cnpj' => [
                'required',
                'string',
                'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$|^\d{14}$/',
            ],
            'data_vencimento' => [
                'required',
                'date',
                'date_format:Y-m-d',
            ],
            'nome_empresa' => [
                'required',
                'string',
                'max:255',
                'min:2',
            ],
            'arquivo' => [
                'required',
                'file',
                'mimes:xml,pdf',
                'max:10240', // 10MB máximo
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.regex' => 'O CNPJ deve estar no formato 00.000.000/0000-00 ou conter apenas 14 dígitos.',
            'data_vencimento.required' => 'A data de vencimento é obrigatória.',
            'data_vencimento.date' => 'A data de vencimento deve ser uma data válida.',
            'data_vencimento.date_format' => 'A data de vencimento deve estar no formato YYYY-MM-DD.',
            'nome_empresa.required' => 'O nome da empresa é obrigatório.',
            'nome_empresa.max' => 'O nome da empresa não pode ter mais de 255 caracteres.',
            'nome_empresa.min' => 'O nome da empresa deve ter pelo menos 2 caracteres.',
            'arquivo.required' => 'O arquivo da nota fiscal é obrigatório.',
            'arquivo.file' => 'Deve ser enviado um arquivo válido.',
            'arquivo.mimes' => 'O arquivo deve ser do tipo XML ou PDF.',
            'arquivo.max' => 'O arquivo não pode ser maior que 10MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Remove formatação do CNPJ se vier formatado
        if ($this->cnpj) {
            $this->merge([
                'cnpj' => preg_replace('/\D/', '', $this->cnpj),
            ]);
        }
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'cnpj' => 'CNPJ',
            'data_vencimento' => 'data de vencimento',
            'nome_empresa' => 'nome da empresa',
            'arquivo' => 'arquivo da nota fiscal',
        ];
    }
}

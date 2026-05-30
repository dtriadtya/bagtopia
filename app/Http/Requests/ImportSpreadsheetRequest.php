<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportSpreadsheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'csv_file' => ['required_without:master_file', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'],
            'master_file' => ['required_without:csv_file', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'],
        ];
    }

    public function spreadsheetFile(): \Illuminate\Http\UploadedFile
    {
        return $this->file('csv_file') ?? $this->file('master_file');
    }
}

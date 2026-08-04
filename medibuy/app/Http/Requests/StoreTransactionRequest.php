<?php
// app/Http/Requests/StoreTransactionRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
  public function authorize(): bool { return auth()->check(); } // afina con Policies
  public function rules(): array {
    return [
      'type' => ['required', Rule::in(['allocation','disbursement','return'])],
      'manager_id' => ['required','exists:users,id'],
      'counterparty_id' => ['required','exists:users,id'],
      'amount' => ['required','numeric','min:0.01'],
      'purpose' => ['nullable','string','max:255'],
      // firmas en base64 dataURL
      'manager_signature' => ['required','string'],
      'counterparty_signature' => ['required','string'],
      // evidencia obligatoria SOLO para devoluciones
      'evidence.*' => ['required_if:type,return','file','mimes:jpg,jpeg,png,pdf','max:5120'],
      // NIP obligatorio para entregas a usuarios
      'nip' => ['required_if:type,disbursement','nullable','digits_between:4,8'],
    ];
  }
  public function messages(): array {
    return [
      'evidence.*.required_if' => 'La devolución requiere evidencia (foto/ticket/factura).',
      'nip.required_if' => 'Para entregar dinero debes autorizar con tu NIP.',
    ];
  }
}

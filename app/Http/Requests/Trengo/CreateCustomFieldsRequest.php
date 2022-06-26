<?php

namespace App\Http\Requests\Trengo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCustomFieldsRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'custom_fields' => 'required|array|min:1',
      'custom_fields.*.title' => 'required|string',
      'custom_fields.*.type' => ['required', Rule::in(['PROFILE', 'CONTACT', 'TICKET'])],
    ];
  }
}

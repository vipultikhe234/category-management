<?php

namespace App\Http\Requests;

use App\Http\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;


class CategoryManagementRequest extends FormRequest
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
        $isUpdateRequest = str_contains($this->path(), 'update');
        if ($isUpdateRequest) {
            return [
                'id' => 'required|integer|exists:category_model,id',
                'category_name' => [
                    'nullable',
                    'string',
                    'max:100',
                    Rule::unique('category_model', 'category_name')->ignore($this->id, 'id'),
                ],
                'category_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2024',
                'parent_category' => 'nullable|integer',
                'status' => 'nullable|string|in:ON,OFF'
            ];
        } elseif (str_contains($this->path(), 'get_category_by_id')) {
            return [
                'id' => 'required|integer|exists:category_model,id',
            ];
        } else {
            return [
                'category_name' => 'required|string|max:100|unique:category_model,category_name',
                'category_image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2024',
                'parent_category' => 'nullable|integer',
            ];
        }
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ApiResponseHelper::validationError($validator));
    }
}

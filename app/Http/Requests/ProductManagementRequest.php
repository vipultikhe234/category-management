<?php

namespace App\Http\Requests;

use App\Http\Helpers\ApiResponseHelper;
use App\Rules\CheckUnique;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductManagementRequest extends FormRequest
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
        if (str_contains($this->path(), 'insert')) {
            return $this->onCreate();
        }
        if (str_contains($this->path(), 'update')) {
            return $this->onUpdate();
        }
    }
    public function onCreate(): array
    {
        return [
            'product_name' => [
                'required',
                'string',
                'max:100',
                new CheckUnique('product_model', 'product_name', 'category_id', $this->category_id)
            ],
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_description' => 'required|string',
            'category_id' => 'required|integer|exists:category_model,id',
        ];
    }
    public function onUpdate(): array
    {
        return [
            'id' => 'required|integer|exists:product_model,id',
            'product_name' => [
                'nullable',
                'string',
                'max:100',
                new CheckUnique('product_model', 'product_name', 'category_id', $this->category_id, $this->id)
            ],
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_description' => 'nullable|string',
            'category_id' => 'nullable|integer|exists:category_model,id',
            'status' => 'required|string|in:ON,OFF'
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ApiResponseHelper::validationError($validator));
    }
}

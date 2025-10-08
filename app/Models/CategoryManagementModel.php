<?php

namespace App\Models;

use App\Http\Helpers\ApiResponseHelper;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryManagementModel extends Model
{
    use HasFactory;
    protected $table = 'category_model';
    protected $primaryKey = 'id';
    protected $fillable = [
        'category_name',
        'category_image',
        'parent_category',
        'status'
    ];

    public static function insertCategory($categoryData)
    {
        try {
            $hasCategoryInserte = CategoryManagementModel::create($categoryData);
            if ($hasCategoryInserte) {
                $message = __('message.CATEGORY_INSERTED');
                return ApiResponseHelper::success($message, 'category_id', $hasCategoryInserte->id);
            } else {
                $message = __('message.CATEGORY_FAILED');
                return ApiResponseHelper::error($message, JsonResponse::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
    public static function updateCategory($categoryData)
    {
        try {
            $category = CategoryManagementModel::find($categoryData['id']);
            if ($category) {
                $category->update($categoryData);
                $message = __('message.CATEGORY_UPDATED');
                return ApiResponseHelper::success($message, 'category_id', $categoryData['id']);
            }
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
    public static function getCategoryListing()
    {
        try {
            $categoryData = CategoryManagementModel::select('id', 'category_name', 'category_image', 'status')
                ->where('status', 'ON')
                ->get();
            if ($categoryData->count() > 0) {
                $message = __('message.CATEGORY_FETCHED');
                return ApiResponseHelper::success($message, 'data', $categoryData);
            } else {
                $message = __('message.CATEGORY_NOT_FOUND');
                return ApiResponseHelper::success($message, null);
            }
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
    public static function getCategoryByID($categoryId)
    {
        try {
            $categoryData = CategoryManagementModel::select('id', 'category_name', 'category_image', 'status')
                ->where('id', $categoryId)
                ->first();
            if ($categoryData) {
                $message = __('message.CATEGORY_FETCHED');
                return ApiResponseHelper::success($message, 'data', $categoryData);
            } else {
                $message = __('message.CATEGORY_NOT_FOUND');
                return ApiResponseHelper::success($message, null);
            }
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
    public static function getCategoryDropdown()
    {
        try {
            $categoryData = CategoryManagementModel::select('id', 'category_name')
                ->where('status', 'ON')
                ->get();
            if ($categoryData->count() > 0) {
                $message = __('message.CATEGORY_FETCHED');
                return ApiResponseHelper::success($message, 'data', $categoryData);
            } else {
                $message = __('message.CATEGORY_NOT_FOUND');
                return ApiResponseHelper::success($message, null);
            }
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
}

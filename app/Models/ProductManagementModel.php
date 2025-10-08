<?php

namespace App\Models;

use App\Http\Helpers\ApiResponseHelper;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductManagementModel extends Model
{
    use HasFactory;
    protected $table = 'product_model';
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_name',
        'product_image',
        'product_description',
        'category_id',
        'status'
    ];

    public static function insertProduct($productData)
    {
        try {
            $hasProductInserte = self::create($productData);
            if ($hasProductInserte) {
                $message = __('message.PRODUCT_INSERTED');
                return ApiResponseHelper::success($message, 'product_id', $hasProductInserte->id);
            } else {
                $message = __('message.PRODUCT_FAILED');
                return ApiResponseHelper::error($message, JsonResponse::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
    public static function updateProduct($productData)
    {
        try {
            $product = self::find($productData['id']);
            if ($product) {
                $product->update($productData);
                $message = __('message.PRODUCT_UPDATED');
                return ApiResponseHelper::success($message, 'product_id', $productData['id']);
            }
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
    public static function getProductListing($category_id)
    {
        try {
            $productData = self::select('product_model.id', 'product_name', 'product_image', 'product_description', 'product_model.category_id', 'category_model.category_name', 'product_model.status')
                ->leftjoin('category_model', 'product_model.category_id', '=', 'category_model.id')
                ->where('product_model.status', 'ON');
            if ($category_id) {
                $productData->where('product_model.category_id', $category_id);
            }
            $productData = $productData->get();
            if ($productData->count() > 0) {
                $message = __('message.PRODUCT_FETCHED');
                return ApiResponseHelper::success($message, 'data', $productData);
            } else {
                $message = __('message.PRODUCT_NOT_FOUND');
                return ApiResponseHelper::success($message, null);
            }
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
    public static function getProductByID($productId)
    {
        try {
            $productData = self::select('product_model.id', 'product_name', 'product_image', 'product_description', 'product_model.category_id', 'category_model.category_name', 'product_model.status')
                ->leftjoin('category_model', 'product_model.category_id', '=', 'category_model.id')
                ->where('product_model.id', $productId)
                ->first();
            if ($productData) {
                $message = __('message.PRODUCT_FETCHED');
                return ApiResponseHelper::success($message, 'data', $productData);
            } else {
                $message = __('message.PRODUCT_NOT_FOUND');
                return ApiResponseHelper::success($message, null);
            }
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
}

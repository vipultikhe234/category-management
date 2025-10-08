<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponseHelper;
use App\Http\Helpers\FileUploadHelper;
use App\Http\Requests\ProductManagementRequest;
use App\Models\ProductManagementModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductManagementController extends Controller
{
    /**
     * Insert a new product into the database.
     *
     * This method handles the insertion of a new product along with its image.
     * It validates incoming request data, uploads and resizes the image using the
     * FileUploadHelper, and stores the product information in the database.
     *
     * @param  \App\Http\Requests\ProductManagementRequest  $request
     *         Validated request object containing product data and image file.
     *
     * @return \Illuminate\Http\JsonResponse
     *         JSON response indicating success or failure of the operation.
     *
     * @throws \Exception
     *         If any unexpected error occurs during the process.
     */
    public function insertProductData(ProductManagementRequest $request)
    {
        try {
            $productData = $request->validated();
            if ($request->hasFile('product_image')) {
                $latestId = ProductManagementModel::max('id');
                $newId = $latestId ? $latestId + 1 : 1;
                $image = $request->file('product_image');
                $path = FileUploadHelper::destination();
                $file_path = $path['product'] . $newId;
                if (!Storage::exists($file_path)) {
                    Storage::makeDirectory($file_path, 0777, true, true);
                }
                $productData['product_image'] = FileUploadHelper::insertImage($image, $file_path, 'product_image');
            }
            return ProductManagementModel::insertProduct($productData);
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }

    /**
     * Handle the request to update an existing product.
     *
     * This method performs the following operations:
     * - Validates the incoming request using `ProductManagementRequest` rules.
     * - Fetches the existing product record by ID.
     * - If a new product image is uploaded, it is processed and saved in a 
     *   dynamically generated directory based on the product ID.
     * - If no new image is uploaded, the existing image remains unchanged.
     * - Updates the product record in the database using the validated data.
     * - If the product's status is set to OFF, it is considered logically deleted.
     *   The product and its image remain in the system but are excluded from 
     *   future listings.
     *
     * Note: To "delete" a product, simply update its status to OFF —
     * it will no longer appear in active listings but will still exist in the system.
     *
     * @param  \App\Http\Requests\ProductManagementRequest  $request
     *         Validated request object containing product data and optional image file.
     * 
     * @return \Illuminate\Http\JsonResponse
     *         JSON response indicating success or failure of the operation.
     *
     * @throws \Exception
     *         If any unexpected error occurs during the process.
     */
    public function updateProductData(ProductManagementRequest $request)
    {
        try {

            $productData = $request->validated();
            if ($request->hasFile('product_image')) {
                $image = $request->file('product_image');
                $path = FileUploadHelper::destination();
                $file_path = $path['product'] . $request->id;
                if (!Storage::exists($file_path)) {
                    Storage::makeDirectory($file_path, 0777, true, true);
                }
                $productData['product_image'] = FileUploadHelper::insertImage($image, $file_path, 'product_image');
            }
            return ProductManagementModel::updateProduct($productData);
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }

    /**
     * Retrieve the list of active products with their category details.
     *
     * This method performs the following operations:
     * - Validates the optional `category_id` parameter.
     * - Fetches products from `product_model` where status is 'ON'.
     * - If `category_id` is provided, filters the products accordingly.
     * - Joins the `category_model` to include the category name for each product.
     * - Selects relevant fields: 'id', 'product_name', 'product_image', 'category_id', 'category_name', and 'status'.
     *
     * Response Scenarios:
     * - If matching products are found: returns a success response with a message and product list.
     * - If no products are found: returns a success response with a 'not found' message and null data.
     * - If validation fails: returns a validation error response.
     * - If an exception occurs: returns an internal server error with the exception message.
     *
     * @param \Illuminate\Http\Request $request
     *     HTTP request object containing optional 'category_id' as query parameter.
     *
     * @return \Illuminate\Http\JsonResponse
     *     JSON response containing:
     *     - status: 1 (success) or 0 (error)
     *     - message: Descriptive message
     *     - data (if available): List of active product records with category details or null
     */
    public function getProduct(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'nullable|integer|exists:category_model,id',
            ]);
            if ($validator->fails()) {
                return ApiResponseHelper::validationError($validator);
            }
            return ProductManagementModel::getProductListing($request->category_id);
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }

    /**
     * Retrieve product details by its ID.
     *
     * This method performs the following operations:
     * - Validates the incoming request to ensure a valid `id` is provided.
     * - Fetches a specific product from the `product_model` table using the given ID.
     * - Joins the `category_model` to retrieve the associated category name.
     * - Selects the fields:
     *   - product_model.id
     *   - product_name
     *   - product_image
     *   - product_model.category_id
     *   - category_model.category_name
     *   - product_model.status
     *
     * Response Scenarios:
     * - If a product with the given ID exists: returns a success response with product details.
     * - If not found: returns a success response with a "not found" message and null data.
     * - If validation fails: returns a validation error response.
     * - If an exception occurs: returns an internal server error with the exception message.
     *
     * @param \Illuminate\Http\Request $request
     *     HTTP request object containing 'id' as a required parameter.
     *
     * @return \Illuminate\Http\JsonResponse
     *     JSON response containing:
     *     - status: 1 (success) or 0 (error)
     *     - message: Descriptive message
     *     - data: product record if found, otherwise null
     */
    public function getProductDataByID(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:product_model,id'
            ]);
            if ($validator->fails()) {
                return ApiResponseHelper::validationError($validator);
            }
            return ProductManagementModel::getProductByID($request->id);
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
}

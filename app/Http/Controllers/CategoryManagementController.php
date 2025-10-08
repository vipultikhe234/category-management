<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponseHelper;
use App\Http\Helpers\FileUploadHelper;
use App\Http\Requests\CategoryManagementRequest;
use App\Models\CategoryManagementModel;
use Exception;
use Illuminate\Support\Facades\Storage;

class CategoryManagementController extends Controller
{
    /**
     * Insert a new category into the database.
     *
     * This method handles the insertion of a new category along with its image.
     * It validates incoming request data, uploads and resizes the image using the
     * FileUploadHelper if present, and stores the category information in the database.
     *
     * @param  \App\Http\Requests\CategoryManagementRequest  $request
     *         Validated request object containing category data and image file.
     *
     * @return \Illuminate\Http\JsonResponse
     *         JSON response indicating success or failure of the operation.
     *
     * @throws \Exception
     *         If any unexpected error occurs during the process.
     */
    public function insertCategoryData(CategoryManagementRequest $request)
    {
        try {
            $categoryData = $request->validated();
            if ($request->hasFile('category_image')) {
                $latestId = CategoryManagementModel::max('id');
                $newId = $latestId ? $latestId + 1 : 1;
                $image = $request->file('category_image');
                $path = FileUploadHelper::destination();
                $file_path = $path['category'] . $newId;
                if (!Storage::exists($file_path)) {
                    Storage::makeDirectory($file_path, 0777, true, true);
                }
                $categoryData['category_image'] = FileUploadHelper::insertImage($image, $file_path, 'category_image');
            }
            return CategoryManagementModel::insertCategory($categoryData);
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
    /**
     * Handle the request to update an existing category.
     *
     * This method performs the following operations:
     * - Validates the incoming request using `CategoryManagementRequest` rules.
     * - If a new category image is uploaded, it is processed and saved in a 
     *   dynamically generated directory based on the category ID.
     * - If no new image is uploaded, the existing image remains unchanged.
     * - Updates the category record in the database.
     * - If the category's status is set to OFF, it is considered logically deleted.
     *   The category and its image remain in the system but are excluded from 
     *   future listings. 
     *
     * Note: To "delete" a category, simply update its status to OFF — 
     * it will no longer appear in active listings but will still exist in the system.
     *
     * @param  \App\Http\Requests\CategoryManagementRequest  $request
     *         Validated request object containing category data and optional image file.
     * 
     * @return \Illuminate\Http\JsonResponse
     *         JSON response indicating success or failure of the operation.
     *
     * @throws \Exception
     *         If any unexpected error occurs during the process.
     */
    public function updateCategoryData(CategoryManagementRequest $request)
    {
        try {

            $categoryData = $request->validated();
            if ($request->hasFile('category_image')) {
                $image = $request->file('category_image');
                $path = FileUploadHelper::destination();
                $file_path = $path['category'] . $request->id;
                if (!Storage::exists($file_path)) {
                    Storage::makeDirectory($file_path, 0777, true, true);
                }
                $categoryData['category_image'] = FileUploadHelper::insertImage($image, $file_path, 'category_image');
            }
            return CategoryManagementModel::updateCategory($categoryData);
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
    /**
     * Retrieve the list of active categories.
     *
     * This method fetches all categories from the database where the status is 'ON'.
     * It selects only relevant fields: 'id', 'category_name', 'category_image', and 'status'.
     *
     * Response Structure:
     * - On success with data: returns a success response with a message and category data.
     * - On success with no data: returns a success response with a 'not found' message and null data.
     * - On failure: returns an internal server error with the exception message.
     *
     * @return \Illuminate\Http\JsonResponse
     *     JSON response with:
     *     - status: 1 (success) or 0 (error)
     *     - message: Descriptive success or failure message
     *     - data (optional): List of category records if available
     */
    public function getCategory()
    {
        try {
            return CategoryManagementModel::getCategoryListing();
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
    /**
     * Retrieve category details by its ID.
     *
     * This method fetches a specific category based on the provided category ID.
     * It selects only the fields: 'id', 'category_name', 'category_image', and 'status'.
     *
     * Response Scenarios:
     * - If a category with the given ID exists: returns a success response with category data.
     * - If not found: returns a success response with a "not found" message and null data.
     * - If an exception occurs: returns a failure response with the error message.
     *
     * @param int $categoryId The ID of the category to retrieve.
     *
     * @return \Illuminate\Http\JsonResponse
     *     JSON response containing:
     *     - status: 1 (success) or 0 (error)
     *     - message: appropriate translated message
     *     - data (if found): category object or null
     */
    public function getCategoryDataByID(CategoryManagementRequest $request)
    {
        try {
            return CategoryManagementModel::getCategoryByID($request->id);
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
    /**
     * Get active categories for dropdown use.
     *
     * This method retrieves all categories from the `category_model` table where the `status` is 'ON'.
     * It returns only the `id` and `category_name` fields, which are suitable for use in a dropdown list.
     *
     * @return \Illuminate\Http\JsonResponse
     *         Success: Returns a JSON response with status 1, message "Category fetched successfully",
     *         and an array of category data (id and category_name).
     *         Failure: If no data is found, returns a JSON response with status 1 and message "Category not found".
     *         Error: On exception, returns a JSON response with status 0 and the exception message.
     *
     * Example Response (Success):
     * {
     *     "status": 1,
     *     "message": "Category fetched successfully.",
     *     "data": [
     *         { "id": 1, "category_name": "Books" },
     *         { "id": 2, "category_name": "Electronics" }
     *     ]
     * }
     */
    public function getCategoryDropdown()
    {
        try {
            return CategoryManagementModel::getCategoryDropdown();
        } catch (Exception $e) {
            return ApiResponseHelper::internalServerError($e->getMessage());
        }
    }
}

<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseHelper
{
    /**
     * Generate a standardized success JSON response.
     *
     * This method returns a structured JSON response typically used for successful API operations.
     *
     * @param string $message Custom success message (default: "Success").
     * @param string $key Key under which the data will be returned (default: "data").
     * @param mixed $data Data to include in the response (optional).
     * @param int $httpStatusCode HTTP status code (default: 200 OK).
     *
     * @return JsonResponse JSON response containing the status, message, and data.
     */
    public static function success(string $message = "Success", $key = 'data', $data = null, int $httpStatusCode = JsonResponse::HTTP_OK): JsonResponse
    {
        if ($key == null) {
            return response()->json([
                "status" => config('constants.status_code.SUCCESS'),
                "message" => $message,
            ], $httpStatusCode);
        }
        return response()->json([
            "status" => config('constants.status_code.SUCCESS'),
            "message" => $message,
            $key => $data
        ], $httpStatusCode);
    }

    /**
     * Generate a standardized error JSON response.
     *
     * This method returns a structured JSON response for failed API operations.
     *
     * @param string $message Custom error message (default: "Error").
     * @param int $httpStatusCode HTTP status code (default: 500 Internal Server Error).
     *
     * @return JsonResponse JSON response containing the failure status and message.
     */
    public static function error(string $message = "Error", int $httpStatusCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return response()->json([
            "status" => config('constants.status_code.FAIL'),
            "message" => $message,
        ], $httpStatusCode);
    }

    /**
     * Generate a structured JSON response for validation errors.
     *
     * This method returns each validation error grouped by field, making it easier
     * for clients to highlight field-specific issues in UI forms.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator The validator instance containing error messages.
     *
     * @return \Illuminate\Http\JsonResponse JSON response with status, generic message, and field-wise error details.
     *
     * Response structure:
     * - status: Failure status code from the config.
     * - message: A general validation failure message.
     * - errors: Field-specific validation error messages.
     */
    public static function validationError($validator): JsonResponse
    {
        return response()->json([
            'status' => config('constants.status_code.FAIL'),
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
    }

    /**
     * Generate a standardized JSON response for internal server errors.
     *
     * This method returns a structured JSON response typically used when an unexpected
     * server-side error occurs. It includes a failure status, a generic error message, 
     * and optional debug information for internal use.
     *
     * @param mixed|null $errorMessages Optional debug or exception details (not shown to end users in production).
     *
     * @return \Illuminate\Http\JsonResponse JSON response indicating a server error.
     *
     * Response structure:
     * - status: Failure status code from config.
     * - message: A user-friendly error message.
     * - error_message: Optional debug message or stack trace (if provided).
     */
    public static function internalServerError($errorMessages = null): JsonResponse
    {
        return response()->json([
            'status' => config('constants.status_code.FAIL'),
            'message' => __('message.INTERNAL_SERVER_ERROR'),
            'error_message' => $errorMessages,
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

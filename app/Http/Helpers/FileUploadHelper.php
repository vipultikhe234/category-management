<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\File;

class FileUploadHelper
{
    public static function destination()
    {
        $path = [
            'category' => 'uplode/category/',
            'product' => 'uplode/product/',
        ];
        return $path;
    }
    /**
     * Uploads and saves an image using Intervention Image library.
     *
     * This method creates a unique filename (if enabled), resizes the image to 100x59 pixels,
     * and stores it under the given `$path` inside the public directory.
     * If the target directory does not exist, it will be created.
     *
     * @param \Illuminate\Http\UploadedFile $file         The uploaded image file.
     * @param string                        $path         The relative path (e.g., 'upload/category/5') where the image should be saved.
     * @param string                        $module_name  Prefix for the image filename, typically representing the module (e.g., 'category_image').
     * @param bool                          $Addunique    Whether to append a unique ID to the filename. Default is true.
     *
     * @return string                                     The relative path to the saved image (used for DB storage or later access).
     *
     * @throws \Intervention\Image\Exception\NotWritableException
     *         If the image could not be written to disk.
     *
     * @example
     * // Usage:
     * $savedPath = FileUploadHelper::insertImage($request->file('image'), 'upload/products/10', 'product_image');
     * // Returns something like: 'upload/products/10/product_image_64ff0b7c4a1a2.jpg'
     */
    public static function insertImage($file, $path, $module_name, $Addunique = true)
    {
        $uniquePart = $Addunique ? '_' . uniqid() : '';
        $extension = $file->getClientOriginalExtension();
        $filename = "/{$module_name}{$uniquePart}.{$extension}";

        $fullPath = public_path($path . $filename);
        $directory = dirname($fullPath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        $image = \Intervention\Image\Facades\Image::make($file)->resize(100, 59)->save($fullPath);
        return $path . $filename;
    }
}

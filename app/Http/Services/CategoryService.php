<?php
namespace App\Http\Services;

use App\Exceptions\CategoryNotFoundException;
use App\Models\Category;

/**
 * Class CategoryService
 * @package App\Http\Services
 */
class CategoryService
{
    public function findById($id)
    {
        $category = Category::where('id', $id)->first();

        if (!$category) {
            throw new CategoryNotFoundException('Category is not found by ID' . $category);
        }

        return $category;
    }
}

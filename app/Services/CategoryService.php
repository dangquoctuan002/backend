<?php
// app/Services/PostService.php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->listWhere();
    }

    public function getCategoryById($id)
    {
        return $this->categoryRepository->findWhere($id);
    }

    // Các phương thức khác để xử lý logic kinh doanh...
}

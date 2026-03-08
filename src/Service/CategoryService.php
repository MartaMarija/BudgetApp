<?php

namespace App\Service;

use App\DTO\Input\CategoryInput;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {}

    public function create(CategoryInput $input, User $user): Category
    {
        $category = new Category();
        $category->setName($input->name);
        $category->setCreatedBy($user);

        return $this->categoryRepository->save($category);
    }

    public function update(Category $category, CategoryInput $input): Category
    {
        $category->setName($input->name);

        return $this->categoryRepository->save($category);
    }
}
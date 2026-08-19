<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;

class MenuProvider
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository
    ) {
    }

    /**
     * @return list<Category>
     */
    public function getMenuCategories(): array
    {
        return $this->categoryRepository->findMenuCategories();
    }
}

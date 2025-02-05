<?php
namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;

class FrontService
{
    protected $categoryRepository;
    protected $packageRepository;

    public function __construct(PackageRepositoryInterface $packageRepository,
    CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->packageRepository = $packageRepository;
    }

    public function searchPackage(string $keyword)
    {
        return $this->packageRepository->searchByName($keyword);
    }

    public function getFrontPageData()
    {
        $categories = $this->categoryRepository->getAllCategories();
        $newPackages = $this->packageRepository->getAllNewPackage();

        return compact('categories', 'newPackages');
    }
}

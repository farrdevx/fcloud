<?php
namespace App\Repositories;

use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;

class PackageRepository implements PackageRepositoryInterface
{
    public function getAllNewPackage()
    {
        return Package::latest()->get();
    }

    public function searchByName(string $keyword)
    {
        return Package::where('name', 'LIKE', '%' . $keyword . '%')->get();
    }

    public function find($id)
    {
        return Package::find($id);
    }

    public function getPrice($packageId)
    {
        $package = $this->find($packageId);
        return $package ? $package->price : 0;
    }
}

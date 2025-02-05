<?php

namespace App\Repositories\Contracts;

interface PackageRepositoryInterface
{
    public function getAllNewPackage();

    public function find($id);

    public function getPrice($ticketId);

    public function searchByName(string $keyword);
}

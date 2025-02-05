<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Package;
use App\Services\FrontService;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    //
    protected $frontService;

    public function __construct(FrontService $frontService) //DIP depedency Injection
    {
        $this->frontService = $frontService;
    }

    public function index()
    {
        $data = $this->frontService->getFrontPageData();
        return view('front.index', $data);
    }

    public function details(Package $package)
    {
        return view('front.details', compact('package'));
    }

    public function category(Category $category)
    {
        return view('front.category', compact('category'));
    }
}

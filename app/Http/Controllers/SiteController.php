<?php

namespace App\Http\Controllers;
use App\Models\Category;


class SiteController extends Controller
{
    public function index()
    {
        $data['pageTitle'] = 'Home';
        $data['categories']       = Category::where('status', 1)->orderBy('sort_order', 'asc')
        ->select(['id', 'name', 'slug', 'image'])
        ->get();
        // rendom 5 catagories
        $data['randomCategories'] = Category::where('status', 1)->inRandomOrder()->select(['name', 'slug'])->limit(5)->get();
        return view('theme.home', $data);
    }
}
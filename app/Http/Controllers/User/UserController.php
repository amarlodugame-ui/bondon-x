<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;

class UserController extends Controller
{


    public function home()
    {
        $data['pageTitle']        = 'Dashboard';
        $data['categories']       = Category::where('status', 1)->orderBy('sort_order', 'asc')
        ->select(['id', 'name', 'slug', 'image'])
        ->get();
        return view('theme.user.dashboard', $data);
    }
    

}

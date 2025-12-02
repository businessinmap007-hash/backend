<?php

namespace App\Http\Controllers\Admin;

use App\Agency;
use App\Models\Category;
use App\Comment;
use App\Company;
use App\Membership;
use App\Offer;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use willvincent\Rateable\Rating;
use App\Libraries\Main;


class HomeController extends Controller
{


    public $main;

    public function __construct(Main $main)
    {
        $this->main = $main;
    }

    public function index()
    {
         if (!auth()->check()) 
            return redirect(route('admin.login'));

        return view('admin.home.index');
    }
}

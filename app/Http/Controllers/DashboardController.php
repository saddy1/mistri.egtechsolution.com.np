<?php

namespace App\Http\Controllers;


use App\Models\Student;
use App\Models\Admin;


class DashboardController extends Controller
{
    public function home()
    {
        return view('welcome');
    }

    public function admin()
    {
        $admin = Admin::find(session('admin_id'));
 
      return view('dashboard.index', compact('admin'));
     }
}

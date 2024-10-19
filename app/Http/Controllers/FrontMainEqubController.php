<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontMainEqubController extends Controller
{
    public function index() {

        return view('admin/mainEqub/indexMain');
    }
}

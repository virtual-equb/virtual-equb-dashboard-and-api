<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainEqub;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MainEqubController extends Controller
{
    private $title;

    public function __construct()
    {
        $this->title = "Virtual Equb - Main Equb";
    }

    public function index()
    {
        try {
            $userData = Auth::user();
            if ($userData && in_array($userData['role'], [
                "admin", 
                "member", 
                "general_manager", 
                "operation_manager", 
                "it", 
                "customer_service", 
                "assistant"
            ])) {
                $data['title'] = $this->title;
                $data['mainEqubs'] = MainEqub::all(); // Fetch all MainEqub records
                return view('admin/mainEqub.mainEqubList', $data);
            } else {
                return view('auth/login');
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            Session::flash('error', $msg);
            return back();
        }
    }
}
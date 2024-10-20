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
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Adjust as needed
            'remark' => 'nullable|string',
        ]);

        // Create a new Main Equb instance
        $mainEqub = new MainEqub();
        $mainEqub->name = $request->name;
        $mainEqub->created_by =1;
        if ($request->hasFile('image')) {
            // Store the image and get the path
            $path = $request->file('image')->store('equb_images', 'public');
            $mainEqub->image = $path; // Save the path in the database
        }
        $mainEqub->remark = $request->remark;

        // Save the Main Equb to the database
        $mainEqub->save();

        // Redirect or return a response
        return redirect()->route('mainEqubs.index')->with('success', 'Main Equb added successfully.');
    }
}
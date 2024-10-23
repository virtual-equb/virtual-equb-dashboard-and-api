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
        $mainEqub->created_by = Auth::id(); // Store the ID of the authenticated user
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

    public function show($id)
    {
        // $mainEqub = MainEqub::findOrFail($id);
       // dd($mainEqub);

         // Retrieve the equb by ID
         $equb = MainEqub::findOrFail($id);
        
         // Return the data as JSON
         return response()->json($equb);
    }

    public function edit($id)
    {
        $mainEqub = MainEqub::findOrFail($id);
        return response()->json($mainEqub); // Return the data as JSON for the AJAX request
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'remark' => 'nullable|string|max:500',
            'status' => 'required|boolean',
        ]);
    
        $equb = MainEqub::findOrFail($id);
        $equb->name = $request->input('name');
        $equb->remark = $request->input('remark');
        $equb->active = $request->input('status');
        $equb->save();
    
        return response()->json(['message' => 'Main Equb updated successfully!']);
    }

    public function destroy($id)
    {
        try {
            $mainEqub = MainEqub::findOrFail($id);
            $mainEqub->delete(); // Delete the Main Equb record

            return redirect()->route('mainEqubs.index')->with('success', 'Main Equb deleted successfully.');
        } catch (Exception $ex) {
            $msg = "Unable to delete the Main Equb, please try again!";
            Session::flash('error', $msg);
            return back();
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Exception;
use App\Models\MainEqub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use App\Repositories\MainEqub\MainEqubRepositoryInterface;

class MainEqubController extends Controller
{
    private $title;
    private $activityLogRepository;
    private $mainEqubRepository;

    public function __construct(
        IMemberRepository $memberRepository,
        IActivityLogRepository $activityLogRepository,
        MainEqubRepositoryInterface $mainEqubRepository
    ) {
        $this->title = "Virtual Equb - Main Equb";
        $this->activityLogRepository = $activityLogRepository;

        // Permission Guard
        // $this->middleware('permission_check_logout:update main_equb', ['only' => ['update', 'edit']]);
        // $this->middleware('permission_check_logout:delete main_equb', ['only' => ['destroy']]);
        // $this->middleware('permission_check_logout:view main_equb', ['only' => ['index', 'show']]);
        // $this->middleware('permission_check_logout:create main_equb', ['only' => ['store', 'create']]);
    }

    public function index()
    {
        try {
            $userData = Auth::user();
            $data['title'] = $this->title;
            $data['mainEqubs'] = MainEqub::all(); // Fetch all MainEqub records
            return view('admin/mainEqub.mainEqubList', $data);
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            Session::flash('error', $msg);
            return back();
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        // Validate the incoming request data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
            'remark' => 'nullable|string',
            'active' => 'nullable'
        ]);
        $data['created_by'] = Auth::id();
        $mainEqub = MainEqub::create($data);
        $mainEqubs = $this->mainEqubRepository->all();
        if ($mainEqub) {
            $activityLog = [
                'type' => 'main_equbs',
                'type_id' => $mainEqub->id,
                'action' => 'created',
                'user_id' => $user->id,
                'username' => $user->name
            ];
            ActivityLog::create($activityLog);
            $msg = "Main Equb has been created successfully";
            $type = 'success';
            Session::flash($type, $msg);
        }

        // Redirect or return a response
        return redirect()->route('mainEqubs.index', ['mainEqubs' => $mainEqubs])->with('success', 'Main Equb added successfully.');
    }

    public function show($id)
    {
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

    public function update1(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'remark' => 'nullable|string|max:500',
            'status' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 

        ]);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/equbs', 'public'); // Store the image
            $data['image'] = $imagePath; // Add the image path to the data array
        }

        $equb = MainEqub::findOrFail($id);
        $equb->name = $request->input('name');
        $equb->remark = $request->input('remark');
        $equb->active = $request->input('status');
        $equb->save();
    
        return response()->json(['message' => 'Main Equb updated successfully!']);
    }
    public function update(Request $request, $id)
    {
        // Return the JSON representation of the request data for debugging
        // This line is for debugging purposes; you can remove it later
        // return response()->json($request->all());
    
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'remark' => 'nullable|string|max:500', // Added validation for remark
            'active' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Added image validation
        ]);
    
        // Find the MainEqub model by ID or fail
        $equb = MainEqub::findOrFail($id);
    
        // Update the model attributes
        $equb->name = $validatedData['name'];
        $equb->remark = $validatedData['remark'] ?? $equb->remark; // Keep existing remark if not provided
        $equb->active = $validatedData['active'];
    
        // Handle the image upload if a new image is provided
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/equbs', 'public'); // Store the image
            $equb->image = $imagePath; // Update the image path in the model
        }
    
        // Save the updated model
        $equb->save();
    
        return response()->json(['message' => 'Equb updated successfully!']);
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
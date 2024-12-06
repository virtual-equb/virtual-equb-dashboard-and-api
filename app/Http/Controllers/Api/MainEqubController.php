<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\MainEqub;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Repositories\Equb\IEqubRepository;
use App\Http\Requests\MainEqub\UpdateEqubRequest;
use App\Http\Resources\Api\MainEqubResource;
use App\Models\EqubType;

class MainEqubController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        // $this->middleware('api_permission_check:update main_equb', ['only' => ['update', 'edit']]);
        // $this->middleware('api_permission_check:delete main_equb', ['only' => ['destroy']]);
        // $this->middleware('api_permission_check:view main_equb', ['only' => ['index', 'show']]);
        // $this->middleware('api_permission_check:create main_equb', ['only' => ['store', 'create']]);
    }

    public function index() {
        // $userData = Auth::user();
        // dd($userData);
        try {
            
            // $mainEqubs = MainEqub::with('subEqub')->where('subEqub.end_date', '<=', now())->get();
            // Fetch mainEqubs with subEqubs whose end_date is not passed
            // $mainEqubs = MainEqub::with(['subEqub' => function ($query) {
            //     $query->where('end_date', '>=', now());
            // }])->whereHas('subEqub', function ($query) {
            //     $query->where('end_date', '>=', now());
            // })->get();
            $mainEqubs = MainEqub::with(['subEqub' => function ($query) {
                $query->where('end_date', '>=', now());
            }])->get();

            return response()->json([
                'data' => MainEqubResource::collection($mainEqubs),
                'code' => 200,
            ]); 
            
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
        
    }


    public function store(Request $request) {
        $userData = Auth::user();
        
        try {
                $this->validate($request, [
                    'name' => 'required',
                    'created_by' => 'required',
                    // 'image' => 'required',
                    'remark' => 'nullable'
                ]);
                $name = $request->input('name');
                $created_by = $request->input('created_by');
                $image = $request->file('image');
                $remark = $request->input('remark');
    
                $mainEqub = [
                    'name' => $name,
                    'created_by' => $userData->id,
                    'remark' => $remark,
                ];
                if ($request->file('image')) {
                    $image = $request->file('image');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('public/mainEqub', $imageName);
                    $mainEqub['image'] = 'mainEqub/' . $imageName;
                }
                $create = MainEqub::create($mainEqub);
                
                return response()->json([
                    'code' => 200,
                    'message' => 'Successfully Created Main Equb',
                    'data' => new MainEqubResource($create)
                ]);
            
        } catch (Exception $ex) {
            return response()->json([
                'code' => 400,
                'message' => 'Something went wrong!',
                "error" => $ex->getMessage()
            ]);
        }
        
    }

    public function show($id) {
        
        $mainEqub = MainEqub::with(['subEqub' => function ($query) {
            $query->where('end_date', '>=', now());
        }])->whereHas('subEqub', function ($query) {
            $query->where('end_date', '>=', now());
        })->findOrFail($id);

        // $mainEqub = MainEqub::where('id', $id)->with(['subEqub' => function ($query) {
        //     $query->where('end_date', '>=', now());
        // }])->get();

        return response()->json([
            'data' => new MainEqubResource($mainEqub)
        ]);
    }

    public function update($id, Request $request)
    {
        try {
            $userData = Auth::user();
            
            // Fetch the MainEqub by ID
            $mainEqub = MainEqub::where('id', $id)->with('subEqub')->first();

            // Validate the incoming request
            $request->validate([
                'name' => 'required|string',
                'created_by' => 'required|integer',
                'remark' => 'nullable|string',
                'status' => 'nullable|string',
                'active' => 'nullable|boolean',
                'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',  // Image validation
            ]);

            // Build the update array
            $update = [
                'name' => $request->input('name'),
                'created_by' => $userData->id,
                'active' => $request->input('active'),
                'status' => $request->input('status'),
                'remark' => $request->input('remark'),
            ];

            // Handle image upload
            if ($request->file('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/mainEqub', $imageName);
                $update['image'] = 'mainEqub/' . $imageName;
            }

            // Update the MainEqub
            $mainEqub->update($update);

            // Return success response
            return response()->json([
                'data' => new MainEqubResource($mainEqub),
                'code' => 200,
                'message' => 'The Equb was successfully updated'
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Something went wrong: ' . $ex->getMessage()
            ]);
        }
    }

    public function delete() {
        try {
            $mainEqub = MainEqub::findOrFail($id);

            $mainEqub->delete();

            return response()->json([
                'message' => 'Main equb deleted successfully'
            ], 200);

        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ], 500);
        }
    }
}

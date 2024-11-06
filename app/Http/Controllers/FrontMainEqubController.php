<?php

namespace App\Http\Controllers;

use App\Models\MainEqub;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\MainEqub\MainEqubRepositoryInterface;
use App\Repositories\Payment\IPaymentRepository;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontMainEqubController extends Controller
{
    private $paymentRepository;
    private $equbRepository;
    private $mainEqubRepository;
    private $title;

    public function __construct(
        IPaymentRepository $paymentRepository,
        IEqubRepository $equbRepository,
        MainEqubRepositoryInterface $mainEqubRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->equbRepository = $equbRepository;
        $this->mainEqubRepository = $mainEqubRepository;
        $this->title = "Virtual Equb - Dashboard";

        // Permission Guard
        // $this->middleware('permission:update main_equb', ['only' => ['update', 'edit']]);
        // $this->middleware('permission:delete main_equb', ['only' => ['destroy']]);
        // $this->middleware('permission:view main_equb', ['only' => ['index', 'show']]);
        // $this->middleware('permission:create main_equb', ['only' => ['store', 'create']]);
    }

    public function index() {
        $userData = Auth::user();

        try {
            $Equbs = $this->mainEqubRepository->all();
            $countSubEqubs = MainEqub::withCount('subEqub')->get();
            $mainEqubs = $this->mainEqubRepository->all();

            return view('admin/mainEqub/indexMain', [
                'equbs' => $Equbs,
                'title' => $this->title,
                'mainEqubs' => $mainEqubs,
                'countSubEqub' => $countSubEqubs
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 400,
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function show($id) {
        $mainEqub = MainEqub::where('id', $id)->withCount('subEqub')->first();
        $Equb = MainEqub::where('id', $id)->with('subEqub')->first();

        $activeEqubs = MainEqub::where('id', $id)->withCount(['subEqub' => function ($query) {
            $query->where('status', 'Active');
        }])->first();

        $deactiveEqubs = MainEqub::where('id', $id)->withCount(['subEqub' => function ($query) {
            $query->where('status', 'Deactive');
        }])->first();

        $mainEqubs = $this->mainEqubRepository->all();

        return view('admin/mainEqub/viewMain', [
            'activeEqubs' => $activeEqubs,
            'deactiveEqubs' => $deactiveEqubs,
            'Equb' => $Equb,
            'mainEqub' => $mainEqub,
            'title' => $this->title,
            'mainEqubs' => $mainEqubs
        ]);
    }

    public function store(Request $request) {
        $userData = Auth::user();

        try {
            // Validate the request data
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
                'remark' => 'nullable|string',
                'active' => 'nullable'
            ]);
            $data['created_by'] = Auth::id();

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images/equbs', 'public'); // Store the image
                $data['image'] = $imagePath; // Add the image path to the data array
            }

            // Create the main equipment entry
            $mainEqub = MainEqub::create($data);

            // Flash a success message
            $msg = "Main Equb added successfully.";
            $type = 'success';
            Session::flash($type, $msg);

            // Redirect to the index route
            return redirect()->route('mainEqubs.index'); // Adjust the route name as needed
        } catch (Exception $ex) {
            // Return a JSON response for errors
            return response()->json([
                'code' => 400,
                'message' => 'Something went wrong!',
                'error' => $ex->getMessage()
            ]);
        }
    }
}
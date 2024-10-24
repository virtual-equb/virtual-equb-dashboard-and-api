<?php
namespace App\Http\Controllers;

use App\Models\Sub_city;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\SubCity\ISubCityRepository;
use App\Repositories\City\ICityRepository;
use Exception;
use Illuminate\Support\Facades\Response;


class SubCityController extends Controller
{
    private $subCityRepository;
    private $cityRepository;
    private $title;

    public function __construct(ISubCityRepository $subCityRepository, ICityRepository $cityRepository)
    {
        $this->subCityRepository = $subCityRepository;
        $this->cityRepository = $cityRepository;
        $this->title = "Virtual Equb - Sub City";
    }

    /**
     * Display a listing of sub cities for authorized users.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
   /* public function index()
    {
        if (!$this->isAuthorized(Auth::user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $subCities = $this->subCityRepository->getAll();
        $cities = $this->cityRepository->getActiveCity();
        $title =$this->title;
        return view('admin.subCity.subCityList', compact('title','subCities', 'cities')); // Return a view with data
    }*/
    public function index()
    {
        $userData = Auth::user();

        try {
            if ($userData && in_array($userData['role'], ['admin', "equb_collector", "role", "it", 'member'])) {
                $subCities = Sub_city::with('city')->get();
                $cities = Sub_city::with('city')->get();
               $title = "sub City";
                return view('admin.subCity.subCityList', compact('title','subCities', 'cities')); // Return a view with data
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => 'You can\'t perform this action!'
                ]);
            }
        
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => $ex->getMessage()
            ]);
        }
    }
    public function getSubCitiesByCityId($cityId)
    {
        $subCities = $this->subCityRepository->getSubCityByCityId($cityId); // Use the passed cityId
        return response()->json($subCities);
    }

    public function show($id)
    {
        $city = $this->subCityRepository->getSubCityById($id);
        return response()->json($city);
    }

    /**
     * Store a newly created sub city in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'remark' => 'nullable|string',
            'active' => 'boolean',
        ]);

        try {
            Sub_city::create([
                'name' => $request->name,
                'city_id' => $request->city_id,
                'active' => $request->active ?? false,
            ]);
            return redirect()->back()->with('success', 'Sub City added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add Sub City: ' . $e->getMessage());
        }
    }

    /**
     * Check if the user has the required role to access the sub cities.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     * @return bool
     */
    private function isAuthorized($user)
    {
        $allowedRoles = [
            'admin',
            'general_manager',
            'operation_manager',
            'it',
            'finance',
            'customer_service',
            'assistant',
        ];

        return $user && in_array($user->role, $allowedRoles);
    }
}
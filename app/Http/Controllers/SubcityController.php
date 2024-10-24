<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\SubCity\ISubCityRepository;
use App\Repositories\City\ICityRepository;
use Illuminate\Support\Facades\Response;
use App\Models\SubCity;

class SubCityController extends Controller
{
    private $subCityRepository;
    private $cityRepository;
    private $title;

    public function __construct(ISubCityRepository $subCityRepository,ICityRepository $cityRepository)
    {
        $this->subCityRepository = $subCityRepository;
        $this->cityRepository = $cityRepository;
        $this->title = "Virtual Equb - Sub City";

        // Guards
        $this->middleware('permission:edit sub_city', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete sub_city', ['only' => ['destroy']]);
        $this->middleware('permission:view sub_city', ['only' => ['index', 'show']]);
        $this->middleware('permission:create sub_city', ['only' => ['store', 'create']]);
    }

    /**
     * Display a listing of sub cities for authorized users.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $subCities = $this->subCityRepository->getAll(); // Assuming this method retrieves all sub cities
        $cities = $this->cityRepository->getActiveCity();
        return view('admin.subCity.subCityList', [
            'title' => $this->title,
            'subCities' => $subCities,
            'cities' =>$cities
        ]);
    }

    /**
     * Store a newly created sub city in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id', // Ensure the city exists
            'remark' => 'nullable|string', // If you have a remark field
            'active' => 'boolean', // Optional, depending on your needs
        ]);
    
        // Create a new sub city
        SubCity::create([
            'name' => $request->name,
            'city_id' => $request->city_id, // Add city_id to the creation
            'active' => $request->active ?? false, // Default to false if not provided
        ]);
    
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Sub City added successfully!');
    }

    /**
     * Check if the user has the required role to access the sub cities.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     * @return bool
     */
    public function show($id) {
        //
    }

    public function update($id) {
        //
    }

    public function create()
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
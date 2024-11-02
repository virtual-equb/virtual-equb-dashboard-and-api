<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sub_city; // Ensure you're using the correct model here
use Illuminate\Support\Facades\Response;

class SubCityController extends Controller
{
    /**
     * Display a listing of the sub-cities.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
      /*  try {
            $user = Auth::user();

            // Optional: Check user authorization here

            $subCities = Sub_city::with('city', 'subCreater')->get(); // Eager load related models
            $title = "Sub Cities"; // Update title as needed
            $cities = City::all(); // Eager load related models

            return view('admin.subCity.subCityList', compact('title', 'subCities','cities'));
        } catch (\Exception $e) {
            return Response::json(['error' => 'Failed to retrieve sub cities: ' . $e->getMessage()], 500);
        }*/
        return "hello";
    }

    /**
     * Show the form for creating a new sub-city.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $cities = City::all(); // Assuming you have a City model to pull from
        return view('admin.subCityCreate', compact('cities'));
    }

    /**
     * Store a newly created sub-city in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id', // Ensure city exists
            'active' => 'required|boolean',
            'remark' => 'nullable|string|max:255',
        ]);

        Sub_city::create([
            'name' => $request->name,
            'city_id' => $request->city_id,
            'active' => $request->active,
            'remark' => $request->remark,
            'created_by' => Auth::id(), // Set the creator to the authenticated user
        ]);

        return redirect()->route('subcities.index')->with('success', 'Sub-city added successfully!');
    }

    /**
     * Display the specified sub-city.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $subCity = Sub_city::with('city', 'subCreater')->findOrFail($id);
        return response()->json($subCity);
    }

    /**
     * Show the form for editing the specified sub-city.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $subCity = Sub_city::findOrFail($id);
        $cities = City::all(); // Assuming you have a City model to pull from
        return view('admin.subCityEdit', compact('subCity', 'cities'));
    }

    /**
     * Update the specified sub-city in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id', // Ensure city exists
            'active' => 'required|boolean',
            'remark' => 'nullable|string|max:255',
        ]);

        $subCity = Sub_city::findOrFail($id);
        $subCity->update([
            'name' => $request->name,
            'city_id' => $request->city_id,
            'active' => $request->active,
            'remark' => $request->remark,
        ]);

        return response()->json(['message' => 'Sub-city updated successfully.']);
    }

    /**
     * Remove the specified sub-city from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $subCity = Sub_city::findOrFail($id);
        $subCity->delete();

        return response()->json(['message' => 'Sub-city deleted successfully.']);
    }
}
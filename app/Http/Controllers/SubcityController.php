<?php

namespace App\Http\Controllers;

use App\Models\Sub_city;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SubCityController extends Controller
{
    // Display a listing of the sub-cities
    public function index()
    {
        $subCities = Sub_city::all();
        return Response::json($subCities);
    }

    // Store a newly created sub-city
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id', // Assuming a city relationship
        ]);

        $subCity = Sub_city::create([
            'name' => $request->name,
            'city_id' => $request->city_id,
        ]);

        return Response::json($subCity, 201);
    }

    // Display the specified sub-city
    public function show($id)
    {
        $subCity = Sub_city::find($id);

        if (!$subCity) {
            return Response::json(['message' => 'Sub-city not found'], 404);
        }

        return Response::json($subCity);
    }

    // Update the specified sub-city
    public function update(Request $request, $id)
    {
        $subCity = Sub_city::find($id);

        if (!$subCity) {
            return Response::json(['message' => 'Sub-city not found'], 404);
        }

        $request->validate([
            'name' => 'string|max:255',
            'city_id' => 'exists:cities,id',
        ]);

        $subCity->update($request->only(['name', 'city_id']));

        return Response::json($subCity);
    }

    // Remove the specified sub-city
    public function destroy($id)
    {
        $subCity = Sub_city::find($id);

        if (!$subCity) {
            return Response::json(['message' => 'Sub-city not found'], 404);
        }

        $subCity->delete();

        return Response::json(['message' => 'Sub-city deleted successfully']);
    }
}
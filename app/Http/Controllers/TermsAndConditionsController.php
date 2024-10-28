<?php
namespace App\Http\Controllers;

use App\Models\TermsCondition; // Import the model
use Illuminate\Http\Request;

class TermsAndConditionsController extends Controller
{
    /**
     * Display the Terms and Conditions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch the first record from the terms_and_conditions table
        $terms = TermsCondition::all();

        // Check if terms exist
        if (!$terms) {
            abort(404, 'Terms and Conditions not found.'); // Handle case where no terms are found
        }
$title= "terms and condition";
        // Pass the terms to the view
        return view('admin.terms.index', compact('terms','title'));
    }
}
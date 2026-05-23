<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function testimonials()
    {
        $testimonials = Testimonial::where('is_active', true)->orderBy('id', 'desc')->get();
        return response()->json($testimonials);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiDocumentationController extends Controller
{
    /**
     * Display the API documentation page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('api-documentation.index');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class WizardController.
 *
 * @package App\Http\Controllers
 */
class WizardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('search-by-identifier');

        return view('wizard');
    }


}

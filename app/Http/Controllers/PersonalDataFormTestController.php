<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class PersonalDataFormTestController.
 *
 * @package App\Http\Controllers
 */
class PersonalDataFormTestController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function test(Request $request)
    {
        return view('tests.components.personal-data-form', $request->all());
    }
}

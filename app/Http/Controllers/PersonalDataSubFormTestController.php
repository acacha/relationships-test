<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;


/**
 * Class PersonalDataSubFormTestController.
 * 
 * @package App\Http\Controllers
 */
class PersonalDataSubFormTestController
{
    /**
     * index.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function test(Request $request)
    {
        return view('tests.components.personal-data-subform', $request->all());
    }
}
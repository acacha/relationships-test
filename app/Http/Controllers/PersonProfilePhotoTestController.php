<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class PersonProfilePhotoTestController.
 *
 * @package App\Http\Controllers
 */
class PersonProfilePhotoTestController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function test(Request $request)
    {
        return view('tests.components.person-profile-photo', $request->all());
    }
}

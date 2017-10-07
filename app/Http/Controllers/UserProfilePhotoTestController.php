<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class UserProfilePhotoTestController.
 *
 * @package App\Http\Controllers
 */
class UserProfilePhotoTestController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function test(Request $request)
    {
        return view('tests.components.user-profile-photo', $request->all());
    }
}

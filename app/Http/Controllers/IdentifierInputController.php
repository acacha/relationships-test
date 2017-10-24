<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class IdentifierInputController.
 *
 * @package App\Http\Controllers
 */
class IdentifierInputController
{

    /**
     * Test.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function test(Request $request)
    {
        return view('tests.components.identifier-input', $request->all());
    }
}
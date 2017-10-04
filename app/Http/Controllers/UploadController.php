<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class UploadController.
 *
 * @package App\Http\Controllers
 */
class UploadController extends Controller
{
    public function index(Request $request)
    {
        //Authorization
        // -> Manager can upload Photos (using specific permission)
        // -> And user can change is own user profile photo
        //Validation
        // -> Mime type image/-* ?
        // -> Force some size?
        // TODO ->
        //Storage -> use a default one or use storage request parameter?
        // Obtain file name
        // random-hash-{person_id}-{fullname}.extension
        // If no person is proporcionada -> random-hash.extension
        //Save photo to photos table
        // Attach photo to person photos (do not remove other photos!) but change order?
        // We need order? We can user last_update to order cronologically
        // We can use active boolean instead of order?
        // We save then an historic of photos
        dd($request->photo);
        $path = $request->photo->store('photos');
        echo $path;
    }

    // Cruddy design person/photo?

    public function serve($person_id)
    {
        // Return photo for current user
//        return
    }

    /**
     * @param $personId
     */
    public function attachPhotoToPerson($personId, $photoPath)
    {

    }
}

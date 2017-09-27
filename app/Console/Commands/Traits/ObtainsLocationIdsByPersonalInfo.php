<?php

namespace App\Console\Commands\Traits;

use Acacha\Relationships\Models\Location;

/**
 * Class ObtainsLocationIdsByPersonalInfo.
 *
 * @package App\Console\Commands
 */
trait ObtainsLocationIdsByPersonalInfo
{
    /**
     * Obtain location id by personal info.
     *
     * @param $person
     * @return int|null
     */
    protected function obtainLocationIdByPersonalInfo($person)
    {
        if ( $person->person_locality_name == null && $person->postalcode ==null ) return null;
        if ( $person->person_locality_name == "" && $person->postalcode == "" ) return null;

        $locations  = Location::where('name','like',$person->person_locality_name)->get();
        if ($locations->count() != 1) {
            if (!$person->postalcode) return null;
            $locations = $locations->where('postalcode', $person->postalcode);
        }
        if ($locations->count() == 0) {
            if (!$person->postalcode) return null;
            $locations = Location::where('postalcode', $person->postalcode);
        }
        if( $locations->count() == 0 ) return null;
        return $locations->first()->id;
    }
}
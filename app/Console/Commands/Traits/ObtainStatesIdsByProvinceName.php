<?php

namespace App\Console\Commands\Traits;

/**
 * Class ObtainStatesIdsByProvinceName.
 *
 * @package App\Console\Commands
 */
trait ObtainStatesIdsByProvinceName
{
    /**
     * Obtain state id by province name.
     *
     * @param $name
     * @return int|null
     */
    protected function obtainStateIdByProvinceName($name)
    {
        if (! $name) return null;

        switch ($name) {
            case 'Tarragona':
                return 9;
            case 'Castelló':
                return 10;
            case 'Castellò':
                return 10;
            case 'Teruel':
                return 2;
            case 'València':
                return 10;
            case 'Velència':
                return 10;
            case 'Velencia':
                return 10;
            case 'Valencia':
                return 10;
            case 'Jaén':
                return 1;
            case 'Alicante':
                return 10;
            case 'Alacant':
                return 10;
            case 'Alicant':
                return 10;
            case 'Zaragoza':
                return 2;
            case 'Girona':
                return 9;
            case 'Menorca':
                return 4;
            case 'Barcelona':
                return 9;
            default:
                die('Error with: ' . $name);
        }

    }
}
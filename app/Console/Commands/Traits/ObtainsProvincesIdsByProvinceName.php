<?php


namespace App\Console\Commands\Traits;

/**
 * Class ObtainsProvincesIdsByProvinceName.
 *
 * @package App\Console\Commands
 */
trait ObtainsProvincesIdsByProvinceName
{
    /**
     * Obtain province id by province name.
     *
     * @param $name
     * @return int|null
     */
    protected function obtainProvinceIdByProvinceName($name)
    {
        if (! $name) return null;

        switch ($name) {
            case 'Tarragona':
                return 36;
            case 'Castelló':
                return 51;
            case 'Castellò':
                return 51;
            case 'Teruel':
                return 7;
            case 'València':
                return 52;
            case 'Velència':
                return 52;
            case 'Valencia':
                return 52;
            case 'Jaén':
                return 19;
            case 'Alicante':
                return 50;
            case 'Alacant':
                return 50;
            case 'Alicant':
                return 50;
            case 'Zaragoza':
                return 8;
            case 'Girona':
                return 34;
            case 'Menorca':
                return 44;
            case 'Barcelona':
                return 33;
            default:
                die('Error with: ' . $name);
        }

    }
}
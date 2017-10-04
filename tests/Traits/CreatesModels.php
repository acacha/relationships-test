<?php

namespace Tests\Traits;

/**
 * Class CreatesModels.
 *
 * @package Tests\Traits
 */
trait CreatesModels
{
    /**
     * Create a model using laravel factories.
     *
     * @param $class
     * @param array $attributes
     * @param null $times
     * @return mixed
     */
    protected function create($class, $attributes = [], $times = null)
    {
        return factory($class, $times)->create($attributes);
    }

    /**
     * Create models.
     *
     * @param $model
     * @param null $number
     * @return mixed
     */
    private function createModels($model, $number = null) {
        return factory($model , $number)->create();
    }
}
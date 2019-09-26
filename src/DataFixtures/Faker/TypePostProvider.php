<?php

namespace App\DataFixtures\Faker;

use \Faker\Provider\Base as BaseProvider;


class TypePostProvider extends BaseProvider{
    protected static $types = [
        'Annonce',
        'Article'
    ];

    public static function typePost(){
        return static::randomElement(static::$types);
    }
}
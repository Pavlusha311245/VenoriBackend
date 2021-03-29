<?php

namespace App\Exceptions;

use Exception;

class CategoryNotFoundException extends Exception
{
    public function report()
    {

    }

    public function render ($request)
    {
        return view('categories.notfound');
    }
}

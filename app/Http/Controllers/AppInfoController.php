<?php

namespace App\Http\Controllers;

use App\Models\AppInfo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

/**
 * Class AppInfoController
 *
 * @package App\Http\Controllers
 */
class AppInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AppInfo[]|Collection|Response
     */
    public function getInfo()
    {
        return AppInfo::all();
    }
}

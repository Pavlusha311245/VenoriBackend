<?php

namespace App\Http\Controllers;

use App\Models\AppInfo;

class AppInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getInfo(){
        $appInfo = AppInfo::all();
        return response()->json($appInfo,200);
    }
}

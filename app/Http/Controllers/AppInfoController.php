<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppInfoResource;
use App\Models\AppInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $appInfos = AppInfo::all();
        return  response(['appInfo' => AppInfoResource::collection($appInfos), 'message' => 'Retrieved successfully']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'about' => 'required|min10',
            'contact' => 'required',
            'terms' => 'required',
            'privacy_policy' => 'required'
        ]);

        if ($validator->fails()){
            return response(['error' => $validator->errors(), 'Validation error']);
        }

        $appInfos = AppInfo::create([
            'about' => $data[0],
            'contact' => $data[1],
            'terms' => $data[2],
            'privacy_policy' => $data[3]
        ]);
        $appInfos->save();

        return response(['appInfo' => new AppInfoResource($appInfos), 'message' => 'Created successfully'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param AppInfo $appInfos
     * @return \Illuminate\Http\Response
     */
    public function show(AppInfo $appInfos)
    {
        return response(['appInfo' => new AppInfoResource($appInfos), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param AppInfo $appInfos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AppInfo $appInfos)
    {
        $appInfos->update($request->all());
        return response(['appInfo' => new AppInfoResource($appInfos), 'message' => 'Update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AppInfo $appInfos
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(AppInfo $appInfos)
    {
        $appInfos->delete();
        return response(['message' => 'AppInfo is deleted']);
    }
}

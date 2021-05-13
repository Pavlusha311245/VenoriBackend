<?php

namespace App\Http\Controllers;

use App\Models\AppInfo;
use Illuminate\Http\Request;

/**
 * Class AppInfoController
 *
 * @package App\Http\Controllers
 */
class AppInfoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/get_info",
     *     summary="App info",
     *     description="Getting all infos",
     *     operationId="appInfoGetInfo",
     *     tags={"app info"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a list of infos",
     *          @OA\JsonContent(
     *             @OA\Items(type="object", ref="#/components/schemas/AppInfo")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     */
    public function getInfo()
    {
        return AppInfo::all();
    }

    public function addInfo(Request $request)
    {
        $validateAppInfoData = $request->validate([
            'about' => 'required|string',
            'contact' => 'required|string',
            'terms' => 'required|string',
            'privacy_policy' => 'required|string'
        ]);

        $appInfo = AppInfo::create($validateAppInfoData);

        return response()->json($appInfo, 201);
    }

    public function update(Request $request, AppInfo $appInfo)
    {
        $request->validate([
            'about' => 'required|string',
            'contact' => 'required|string',
            'terms' => 'required|string',
            'privacy_policy' => 'required|string'
        ]);

        $appInfo->update($request->all());

        return response()->json($appInfo);
    }
}

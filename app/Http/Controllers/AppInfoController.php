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
     *     path="/api/appInfo",
     *     summary="App info",
     *     description="Getting all infos",
     *     operationId="appInfoGetInfo",
     *     tags={"app info"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting app info",
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
    public function index()
    {
        $appInfo = AppInfo::orderBy('id', 'DESC')->cacheFor(60)->first();

        return response()->json($appInfo);
    }

    /**
     * @OA\Post(
     *     path="/api/appInfo",
     *     summary="App info",
     *     description="Add app info",
     *     operationId="appInfoAddInfo",
     *     tags={"app info"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success adding app info",
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
    public function store(Request $request)
    {
        $validateAppInfo = $request->validate([
            'about' => 'required|string',
            'contact' => 'required|string',
            'terms' => 'required|string',
            'privacy_policy' => 'required|string'
        ]);

        $checkAppInfo = AppInfo::orderBy('id', 'DESC')->first();
        $id = !$checkAppInfo ? 0: $checkAppInfo->id;

        $appInfo = AppInfo::updateOrCreate(
            ['id' => $id],
            [
                'about' => $validateAppInfo['about'],
                'contact' => $validateAppInfo['contact'],
                'terms' => $validateAppInfo['terms'],
                'privacy_policy' => $validateAppInfo['privacy_policy']
            ]
        );

        return response()->json($appInfo);
    }
}

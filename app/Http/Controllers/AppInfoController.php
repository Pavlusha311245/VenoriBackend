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
     *             @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/AppInfo"
     *                  ),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *     ),
     * )
     */
    public function getInfo()
    {
        return AppInfo::all();
    }
}

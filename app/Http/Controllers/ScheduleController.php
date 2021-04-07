<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Controller for create, remove, update and show schedules
 *
 * @package App\Http\Controllers
 */
class ScheduleController extends Controller
{
    /**
     * show all schedules
     * @return Response
     */
    public function index()
    {
        return Schedule::paginate(7);
    }

    /**
     * Method create new schedule
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'place_id' => 'required|numeric|min:1',
        ]);

        $schedule = Schedule::create($request->all());

        return response()->json($schedule, 201);
    }

    /**
     * Method show schedule by id
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        return Schedule::findOrFail($id);
    }

    /**
     * Method update schedule
     * @param Request $request
     * @param Schedule $schedule
     * @return JsonResponse|Response
     */
    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'place_id' => 'numeric|min:1',
        ]);

        $schedule->update($request->all());
        $schedule->save();

        return response()->json($schedule, 200);
    }
}

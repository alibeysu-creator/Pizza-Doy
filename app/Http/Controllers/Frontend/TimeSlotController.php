<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\FrontendTimeSlotResource;
use App\Services\FrontendTimeSlotService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TimeSlotController extends Controller
{
    public FrontendTimeSlotService $frontendTimeSlotService;

    public function __construct(FrontendTimeSlotService $frontendTimeSlotService)
    {
        $this->frontendTimeSlotService = $frontendTimeSlotService;
    }

    public function todayTimeSlot(): Response|AnonymousResourceCollection|Application|ResponseFactory
    {
        try {
            return FrontendTimeSlotResource::collection(
                $this->frontendTimeSlotService->todayTimeSlot(request('order_type'))
            );
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function tomorrowTimeSlot(): Response|AnonymousResourceCollection|Application|ResponseFactory
    {
        try {
            // ✅ Disable tomorrow ordering via config/env switch
            if (!config('app.order_tomorrow_enabled')) {
                // Return empty list (frontend should hide/disable "Tomorrow" if list is empty)
                return FrontendTimeSlotResource::collection(collect([]));
            }

            return FrontendTimeSlotResource::collection(
                $this->frontendTimeSlotService->tomorrowTimeSlot(request('order_type'))
            );
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}

<?php

namespace App\Services;


use App\Libraries\AppLibrary;
use Exception;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\TimeSlot;
use Smartisan\Settings\Facades\Settings;

class FrontendTimeSlotService
{

    public Mixed $now = '';

    /**
     * @throws Exception
     */
    public function todayTimeSlot($orderType = null): \Vanilla\Support\Collection | \IlluminateAgnostic\Str\Support\Collection | \IlluminateAgnostic\Collection\Support\Collection | \IlluminateAgnostic\StrAgnostic\Str\Support\Collection | \IlluminateAgnostic\ArrAgnostic\Arr\Support\Collection | \Illuminate\Support\Collection | \IlluminateAgnostic\Arr\Support\Collection
    {
        try {
            $j                   = 0;
            $times               = [];
            $today               = Carbon::now()->dayOfWeek;
            $defaultScheduleTime = 30;
            $todayTimes          = TimeSlot::select('opening_time', 'closing_time')->where(['day' => $today])->orderBy('opening_time', 'asc')->get()->toArray();

            if ($orderType == \App\Enums\OrderType::DELIVERY) {
                $orderSetup = Settings::group('order_setup')->get('order_setup_delivery_schedule_order_slot_duration');
            } else {
                $orderSetup = Settings::group('order_setup')->get('order_setup_schedule_order_slot_duration');
            }
            if (!empty($orderSetup)) {
                $defaultScheduleTime = (int)$orderSetup;
            }
            foreach ($todayTimes as $time) {
                $arrays = $this->todayTimeSlotCalculation(
                    $defaultScheduleTime,
                    $time['opening_time'],
                    $time['closing_time'],
                    $orderType
                );
                if (count($arrays)) {
                    foreach ($arrays as $array) {
                        $times[$j] = (object)$array;
                        $j++;
                    }
                }
            }
            return collect($times);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function tomorrowTimeSlot($orderType = null): \Vanilla\Support\Collection | \IlluminateAgnostic\Str\Support\Collection | \IlluminateAgnostic\Collection\Support\Collection | \IlluminateAgnostic\StrAgnostic\Str\Support\Collection | \IlluminateAgnostic\ArrAgnostic\Arr\Support\Collection | \Illuminate\Support\Collection | \IlluminateAgnostic\Arr\Support\Collection
    {
        try {
            $tomorrow            = Carbon::tomorrow()->dayOfWeek;
            $defaultScheduleTime = 30;
            $tomorrowTimes       = TimeSlot::select('opening_time', 'closing_time')->where(
                ['day' => $tomorrow]
            )->orderBy(
                'id',
                'asc'
            )->get()->toArray();

            if ($orderType == \App\Enums\OrderType::DELIVERY) {
                $orderSetup = Settings::group('order_setup')->get('order_setup_delivery_schedule_order_slot_duration');
            } else {
                $orderSetup = Settings::group('order_setup')->get('order_setup_schedule_order_slot_duration');
            }

            if (!empty($orderSetup)) {
                $defaultScheduleTime = (int)$orderSetup;
            }

            $tomorrowSlots = [];
            foreach ($tomorrowTimes as $key => $time) {
                $arrays = $this->tomorrowTimeSlotCalculation(
                    $defaultScheduleTime,
                    $time['opening_time'],
                    $time['closing_time']
                );
                if (count($arrays)) {
                    foreach ($arrays as $array) {
                        $tomorrowSlots[] = (object)$array;
                    }
                }
            }
            return collect($tomorrowSlots);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    function todayTimeSlotCalculation($interval, $startTime, $endTime, $orderType = null): array
    {
        $time   = [];
        $strCurrentTime = strtotime(date('H:i'));

        $minimumMinutes = 15;
        if ($orderType == \App\Enums\OrderType::DELIVERY) {
            $minimumMinutes = 50;
        }

        $strStartTime = strtotime($startTime);
        $strEndTime   = strtotime($endTime);

        $strAvailableFrom = strtotime('+' . $minimumMinutes . ' minutes', $strCurrentTime);

        $strStartTime = strtotime('+' . $interval . ' minutes', $strStartTime);

        $i = 0;
        while ($strStartTime <= $strEndTime) {
            $convertStartTime = date('H:i', $strStartTime);
            $nextSlotTime     = strtotime('+' . $interval . ' minutes', $strStartTime);
            $convertEndTime   = date('H:i', $nextSlotTime);

            if ($strStartTime >= $strAvailableFrom) {
                $time[$i]['label']     = str_replace(':', '.', $convertStartTime);
                $time[$i]['from_time'] = $convertStartTime;
                $time[$i]['to_time']   = $convertEndTime;
                $time[$i]['time']      = $convertStartTime;
                $i++;
            }
            $strStartTime = $nextSlotTime;
        }
        return $time;
    }

    function tomorrowTimeSlotCalculation($interval, $startTime, $endTime): array
    {
        $i            = 0;
        $time         = [];
        $strStartTime = strtotime($startTime);
        $strEndTime   = strtotime($endTime);

        $strStartTime = strtotime('+' . $interval . ' minutes', $strStartTime);

        while ($strStartTime <= $strEndTime) {
            $convertStartTime = date('H:i', $strStartTime);
            $nextSlotTime     = strtotime('+' . $interval . ' minutes', $strStartTime);
            $convertEndTime   = date('H:i', $nextSlotTime);

            $time[$i]['label']     = str_replace(':', '.', $convertStartTime);
            $time[$i]['from_time'] = $convertStartTime;
            $time[$i]['to_time']   = $convertEndTime;
            $time[$i]['time']      = $convertStartTime;
            $i++;

            $strStartTime = $nextSlotTime;
        }
        return $time;
    }
}

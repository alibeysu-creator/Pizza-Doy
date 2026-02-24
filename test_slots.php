<?php
date_default_timezone_set('Asia/Dhaka'); // or whatever

function calculateSlots($interval, $startTime, $endTime, $minWaitTime) {
    $time = [];
    $strStartTime = strtotime($startTime);
    $strEndTime   = strtotime($endTime);
    $currentDayTimeStr = '12:03'; // mock current time
    $strCurrentTime = strtotime($currentDayTimeStr);
    
    // The timestamp when orders can start being accepted
    $strAvailableFrom = strtotime('+' . $minWaitTime . ' minutes', $strCurrentTime);
    
    $i = 0;
    while ($strStartTime < $strEndTime) {
        $convertStartTime = date('H:i', $strStartTime);
        $nextSlotTime     = strtotime('+' . $interval . ' minutes', $strStartTime);
        
        // If this exact slot is later than or equal to the available time, we can show it
        if ($strStartTime >= $strAvailableFrom) {
            $time[] = [
                'time' => $convertStartTime
            ];
        }
        $strStartTime = $nextSlotTime;
    }
    return $time;
}

print_r(calculateSlots(15, '10:00', '15:00', 15));
print_r(calculateSlots(50, '10:00', '15:00', 50));

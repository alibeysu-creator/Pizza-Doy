<?php 

namespace App\Libraries;


use App\Enums\Ask;
use App\Enums\Status;


class EnumAppLibrary {
    
    public static function itemFeature($featureType): int
    {
        $featureType = strtolower(trim($featureType));
        if ($featureType === 'yes') {
            return Ask::YES;
        } elseif ($featureType === 'no') {
            return Ask::NO;
        }
        return Ask::NO;
    }

    public static function itemStatus($status): int
    {
        $status = strtolower(trim($status));
        if ($status === 'active') {
            return Status::ACTIVE;
        } elseif ($status === 'inactive') {
            return Status::INACTIVE;
        }
        return Status::INACTIVE;
    }
}
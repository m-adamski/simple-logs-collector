<?php

namespace App\Model\Filter;

enum QuickRange: string {
    case LAST_5_MINUTES = "last_5_minutes";
    case LAST_15_MINUTES = "last_15_minutes";
    case LAST_30_MINUTES = "last_30_minutes";
    case LAST_HOUR = "last_hour";
    case LAST_12_HOURS = "last_12_hours";
    case LAST_24_HOURS = "last_24_hours";
    case TODAY = "today";
    case YESTERDAY = "yesterday";
    case LAST_7_DAYS = "last_7_days";
    case LAST_30_DAYS = "last_30_days";
    case LAST_90_DAYS = "last_90_days";

    public function toString(): string {
        return match ($this) {
            self::LAST_5_MINUTES  => "Last 5 minutes",
            self::LAST_15_MINUTES => "Last 15 minutes",
            self::LAST_30_MINUTES => "Last 30 minutes",
            self::LAST_HOUR       => "Last hour",
            self::LAST_12_HOURS   => "Last 12 hours",
            self::LAST_24_HOURS   => "Last 24 hours",
            self::TODAY           => "Today",
            self::YESTERDAY       => "Yesterday",
            self::LAST_7_DAYS     => "Last 7 days",
            self::LAST_30_DAYS    => "Last 30 days",
            self::LAST_90_DAYS    => "Last 90 days",
            default               => "Custom",
        };
    }
}

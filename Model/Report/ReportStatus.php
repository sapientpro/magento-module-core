<?php

namespace SapientPro\Core\Model\Report;

class ReportStatus
{
    public const DEFAULT = 'default';
    public const X_REPORT = 'x_report';

    /**
     * Get all available statuses
     *
     * @return array
     */
    public static function getAvailableStatuses(): array
    {
        return [
            self::DEFAULT,
            self::X_REPORT,
        ];
    }
}

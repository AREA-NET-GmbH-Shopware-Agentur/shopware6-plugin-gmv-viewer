<?php declare(strict_types=1);

namespace AreanetGmvViewer\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class CalculateGmvTask extends ScheduledTask{
    public static function getTaskName(): string{
        return 'areanet-gmv-viewer.calculate_gmv_task';
    }

    public static function getDefaultInterval(): int{
        return 86400; // 1Tag in Sekunden
    }
}

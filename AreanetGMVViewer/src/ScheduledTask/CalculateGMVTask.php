<?php declare(strict_types=1);

namespace AreanetGMVViewer\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class CalculateGMVTask extends ScheduledTask{
    public static function getTaskName(): string{
        return 'areanet-gmv-viewer.calculate-gmv';
    }

    public static function getDefaultInterval(): int{
        return 86400; // 1 Tag (24h * 60min * 60sek)
    }

    public function __invoke(): void{
        // This method will be executed when the scheduled task runs.
        // You likely want to dispatch the CalculateGmvTask to its handler here.

        // Example of dispatching the task to its handler (if needed):
        // $this->messageBus->dispatch(new CalculateGmvTask());
        //
        // However, for Scheduled Tasks, the handler is typically configured
        // directly to be executed when the task is due. So, the logic
        // to run the GMV calculation should probably be in the handler.

        // It seems you might have tagged the Task class itself as the handler.
        // The handler should be a separate class (CalculateGmvTaskHandler).
        // Remove the messenger.message_handler tag from CalculateGmvTask
        // in your services.xml and ensure CalculateGmvTaskHandler is tagged.
    }

}

<?php declare(strict_types=1);

namespace AreanetGMVViewer\ScheduledTask;

use AreanetGMVViewer\Service\GMVCalculateService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

class CalculateGMVTaskHandler extends ScheduledTaskHandler {
    private GMVCalculateService $GMVCalculateService;

    public function __construct(GMVCalculateService $GMVCalculateService){
        $this->GMVCalculateService = $GMVCalculateService;
    }

    public static function getHandledMessages(): iterable{
        return [CalculateGMVTask::class];
    }

    public function run(): void{
        $currentYear = (int) date('Y');
        $this->calculateAndSaveGMVForYear($currentYear);

        $earliestOrderYear = $this->getEarliestOrderYear();
        for($year = $earliestOrderYear; $year < $currentYear; $year++){
            $gmvExists = $this->gmvDataExistsForYear($year);
            if(!$gmvExists){
                $this->calculateAndSaveGMVForYear($year);
            }
        }
    }

    public function calculateAndSaveGMVForYear(int $year): void{
        $gmv = $this->GMVCalculateService->calculateGMVForYear($year);
        $this->GMVCalculateService->saveGMV($year, $gmv);
    }

    private function getEarliestOrderYear(): int{
        $criteria = new Criteria();
        $criteria->addSorting(new \Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting('orderDate', \Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting::ASCENDING));
        $criteria->setLimit(1);

        $earliestOrder = $this->GMVCalculateService->getContext()->getReadConcern()->getRepository('order')->search($criteria, $this->GMVCalculateService->getContext())->first();
        if($earliestOrder && $earliestOrder->getOrderDate() instanceof \DateTimeInterface){
            return (int) $earliestOrder->getOrderDate()->format('Y');
        }
        return (int) date('Y');
    }

    private function GMVDataExistsForYear(int $year): bool{
        $exists = $this->GMVCalculateService->getConnection()->fetchOne('SELECT id FROM areanet_gmv WHERE year = :year', ['year' => $year]);
        return (bool) $exists;
    }
}
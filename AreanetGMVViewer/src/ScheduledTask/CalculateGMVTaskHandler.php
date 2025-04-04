<?php declare(strict_types=1);

namespace AreanetGmvViewer\ScheduledTask;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: CalculateGmvTask::class)]
class CalculateGmvTaskHandler extends ScheduledTaskHandler
{
    private EntityRepository $orderRepository;
    private EntityRepository $gmvRepository;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        EntityRepository $orderRepository,
        EntityRepository $gmvRepository
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->orderRepository = $orderRepository;
        $this->gmvRepository = $gmvRepository;
    }

    public function run(): void
    {
        $context = Context::createDefaultContext();
        $currentYear = (int)date('Y');

        // Immer das aktuelle Jahr berechnen und speichern
        $this->calculateAndSaveGmvForYear($currentYear, $context);

        // Die letzten zwei Jahre prüfen und ggf. einmalig berechnen
        for ($i = 1; $i <= 2; $i++) {
            $yearToCheck = $currentYear - $i;
            $this->calculateAndSaveGmvIfNotExist($yearToCheck, $context);
        }
    }

    private function calculateAndSaveGmvForYear(int $year, Context $context): void
    {
        $orders = $this->getOrdersForYear($year, $context);
        $gmv = $this->calculateGMV($orders);
        $this->saveGMV($year, $gmv, $context);
    }

    private function calculateAndSaveGmvIfNotExist(int $year, Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('year', $year));
        $existingGmv = $this->gmvRepository->search($criteria, $context)->first();

        if (!$existingGmv) {
            $this->calculateAndSaveGmvForYear($year, $context);
        }
    }

    private function getOrdersForYear(int $year, Context $context): \Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult
    {
        $criteria = new Criteria();
        $criteria->addFilter(new NotFilter(MultiFilter::CONNECTION_AND, [
            new EqualsAnyFilter('stateMachineState.technicalName', [
                'returned',
                'cancelled',
            ]),
        ]));
        $criteria->addFilter(new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter('orderDate', [
            'gte' => (new \DateTimeImmutable())->setDate($year, 1, 1)->setTime(0, 0, 0)->format('Y-m-d H:i:s'),
            'lt' => (new \DateTimeImmutable())->setDate($year + 1, 1, 1)->setTime(0, 0, 0)->format('Y-m-d H:i:s'),
        ]));

        return $this->orderRepository->search($criteria, $context);
    }

    private function getOrders(Context $context): \Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult
    {
        $criteria = new Criteria();
        $criteria->addFilter(new NotFilter(MultiFilter::CONNECTION_AND, [
            new EqualsAnyFilter('stateMachineState.technicalName', [
                'returned',
                'cancelled',
            ]),
        ]));

        return $this->orderRepository->search($criteria, $context);
    }

    private function calculateGMV(\Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult $orders): float
    {
        $gmv = 0.0;

        foreach ($orders as $order) {
            if ($order->getOrderDate() instanceof \DateTimeImmutable) {
                $gmv += $order->getAmountNet();
            }
        }

        return $gmv;
    }

    private function saveGMV(int $year, float $gmv, Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('year', $year));
        $existingGmv = $this->gmvRepository->search($criteria, $context)->first();

        if ($existingGmv) {
            $this->gmvRepository->update([['id' => $existingGmv->getId(), 'gmv' => $gmv]], $context);
        } else {
            $this->gmvRepository->create([
                [
                    'id' => Uuid::randomHex(),
                    'year' => $year,
                    'gmv' => $gmv,
                ],
            ], $context);
        }
    }
}
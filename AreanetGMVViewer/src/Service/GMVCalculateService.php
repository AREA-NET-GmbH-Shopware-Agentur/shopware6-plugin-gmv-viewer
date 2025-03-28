<?php declare(strict_types=1);

namespace AreanetGMVViewer\Service;

use Doctrine\DBAL\Connection;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;

use AreanetGMVViewer\Core\Content\GMV\GmvEntity;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextServiceInterface;

class GMVCalculateService{
    private EntityRepository $orderRepository;
    private EntityRepository $gmvRepository;
    private Connection $connection;

    private SalesChannelContextServiceInterface $salesChannelContextService;
    private SalesChannelContextFactory $salesChannelContextFactory;

    public function __construct(
        EntityRepository $orderRepository,
        EntityRepository $gmvRepository,
        Connection $connection,
        SalesChannelContextServiceInterface $salesChannelContextService,
        SalesChannelContextFactory $salesChannelContextFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->gmvRepository = $gmvRepository;
        $this->connection = $connection;
        $this->salesChannelContextService = $salesChannelContextService;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
    }

    public function calculateGmvForYear(int $year): float{
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('orderDate.year', $year));
        $criteria->addAssociation('lineItems');
        $criteria->addAssociation('currency');

        $context = $this->salesChannelContextFactory->create(null, 'default');
        $orders = $this->orderRepository->search($criteria, $context)->getEntities();

        $gmv = 0.0;
        /** @var OrderEntity $order */
        foreach ($orders as $order) {
            $netPrice = 0.0;
            if ($order->getLineItems()) {
                /** @var OrderLineItemEntity $lineItem */
                foreach ($order->getLineItems() as $lineItem) {
                    $netPrice += $lineItem->getTotalPrice() / (1 + ($lineItem->getPrice()->getCalculatedTaxes()->first() ? $lineItem->getPrice()->getCalculatedTaxes()->first()->getTaxRate() / 100 : 0));
                }
            }
            $gmv += $netPrice - $order->getAmountNet();
        }

        return round($gmv, 4);
    }

    public function saveGmv(int $year, float $gmv): void{
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('year', $year));
        $existingGmv = $this->gmvRepository->search($criteria, $this->getContext())->first();

        if ($existingGmv) {
            $this->gmvRepository->update([
                ['id' => $existingGmv->getId(), 'gmv' => $gmv],
            ], $this->getContext());
        } else {
            $this->gmvRepository->create([
                ['year' => $year, 'gmv' => $gmv],
            ], $this->getContext());
        }
    }

    public function getContext(){
        return $this->salesChannelContextFactory->create(null, 'default');
    }

    public function getConnection(): Connection{
        return $this->connection;
    }
}
<?php declare(strict_types=1);

namespace AreanetGMVViewer\Command;

use AreanetGMVViewer\Service\GMVCalculateService;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateGMVCommand extends Command {
    protected static $defaultName = 'areanet:calculate-gmv';
    private GMVCalculateService $GMVCalculateService;

    public function __construct(GMVCalculateService $GMVCalculateService){
        parent::__construct();
        $this->GMVCalculateService = $GMVCalculateService;
    }

    /**
     * @param Criteria $criteria
     * @return void
     */
    public function getCriteria(Criteria $criteria): void
    {
        $criteria->setLimit(1);
    }

    protected function configure(): void{
        $this->setDescription('Berechnet den GMV für das aktuelle und ggf. vorherige Jahre.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int{
        $currentYear = (int) date('Y');
        $output->writeln(sprintf('Berechne GMV für das Jahr %d...', $currentYear));
        $this->calculateAndSaveGMVForYear($currentYear, $output);

        $earliestOrderYear = $this->getEarliestOrderYear();
        for ($year = $earliestOrderYear; $year < $currentYear; $year++){
            $gmvExists = $this->GMVDataExistsForYear($year);
            if(!$gmvExists){
                $output->writeln(sprintf('Berechne GMV für das Jahr %d...', $year));
                $this->calculateAndSaveGmvForYear($year, $output);
            }else{
                $output->writeln(sprintf('GMV für das Jahr %d ist bereits vorhanden.', $year));
            }
        }

        $output->writeln('GMV-Berechnung abgeschlossen');

        return Command::SUCCESS;
    }

    private function calculateAndSaveGMVForYear(int $year, OutputInterface $output): void{
        $gmv = $this->GMVCalculateService->calculateGMVForYear($year);
        $this->GMVCalculateService->saveGMV($year, $gmv);
        $output->writeln(sprintf('GMV für das Jahr %d: %.4f', $year, $gmv));
        $output->writeln(sprintf('GMV für das Jahr %d berechnet und gespeichert.', $year));
    }

    private function getEarliestOrderYear(): int{
        $criteria = new Criteria();
        $criteria->addSorting(new \Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting('orderDate', \Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting::ASCENDING));
        $this->getCriteria(1);

        $earliestOrder = $this->gmvCalculateService->getContext()->getReadConcern()->getRepository('order')->search($criteria, $this->gmvCalculateService->getContext())->first();
        if ($earliestOrder && $earliestOrder->getOrderDate() instanceof \DateTimeInterface) {
            return (int) $earliestOrder->getOrderDate()->format('Y');
        }
        return (int) date('Y');
    }

    private function GMVDataExistsForYear(int $year): bool{
        $exists = $this->gmvCalculateService->getConnection()->fetchOne('SELECT id FROM areanet_gmv WHERE year = :year', ['year' => $year]);
        return (bool) $exists;
    }
}
<?php declare(strict_types=1);

namespace AreanetGmvViewer\Core\Content\Gmv;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class GmvEntity extends Entity{
    use EntityIdTrait;

    /**
     * @var int
     */
    protected int $year;

    /**
     * @var float
     */
    protected float $gmv;

    public function getYear(): int{
        return $this->year;
    }

    public function setYear(int $year): void{
        $this->year = $year;
    }

    public function getGmv(): float{
        return $this->gmv;
    }

    public function setGmv(float $gmv): void{
        $this->gmv = $gmv;
    }
}
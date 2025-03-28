<?php declare(strict_types=1);

namespace AreanetGMVViewer\Core\Content\GMV;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class GMVEntity extends Entity{
    use EntityIdTrait;

    protected int $year;
    protected int $gmv;

    public function getYear(): int{
        return $this->year;
    }

    public function setYear(int $year): void{
        $this->year = $year;
    }

    public function getGmv(): int{
        return $this->gmv;
    }

    public function setGmv(int $gmv): void{
        $this->gmv = $gmv;
    }

}

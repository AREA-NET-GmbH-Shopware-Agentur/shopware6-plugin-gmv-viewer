<?php declare(strict_types=1);

namespace AreanetGmvViewer\Core\Content\Gmv;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(GmvEntity $entity)
 * @method void set(string $key, GmvEntity $entity)
 * @method GmvEntity[] getIterator()
 * @method GmvEntity[] getElements()
 * @method GmvEntity|null get(string $key)
 * @method GmvEntity|null first()
 * @method GmvEntity|null last()
 */
class GmvCollection extends EntityCollection{
    protected function getExpectedClass(): string{
        return GmvEntity::class;
    }
}

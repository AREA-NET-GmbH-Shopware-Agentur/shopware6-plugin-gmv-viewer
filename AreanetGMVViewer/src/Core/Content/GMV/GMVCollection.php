<?php declare(strict_types=1);

namespace AreanetGMVViewer\Core\Content\GMV;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(GMVEntity $entity)
 * @method void set(string $key, GMVEntity $entity)
 * @method GMVEntity[] getIterator()
 * @method GMVEntity[] getElements()
 * @method GMVEntity|null get(string $key)
 * @method GMVEntity|null first()
 * @method GMVEntity|null last()
 */
class GMVCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return GMVEntity::class;
    }
}

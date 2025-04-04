<?php declare(strict_types=1);

namespace AreanetGmvViewer\Core\Content\Gmv;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;

class GmvDefinition extends EntityDefinition{
    public const ENTITY_NAME = 'areanet_gmv';

    public function getEntityName(): string{
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string{
        return GmvEntity::class;
    }

    public function getCollectionClass(): string{
        return GmvCollection::class;
    }

    protected function defineFields(): FieldCollection{
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new IntField('year', 'year')),
            (new FloatField('gmv', 'gmv')),
            (new DateTimeField('created_at', 'createdAt')),
            (new DateTimeField('updated_at', 'updatedAt')),
        ]);
    }
}

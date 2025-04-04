<?php declare(strict_types=1);

namespace AreanetGmvViewer\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1743748124CreateGmvTable extends MigrationStep{
    public function getCreationTimestamp(): int{
        return 1743748124;
    }

    public function update(Connection $connection): void{
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `areanet_gmv` (
    `id` BINARY(16) NOT NULL,
    `year` INT,
    `gmv` FLOAT,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void{
        $connection->executeStatement("DROP TABLE IF EXISTS `areanet_gmv`;");
    }
}

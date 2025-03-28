<?php declare(strict_types=1);

namespace AreanetGMVViewer\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1743170599 extends MigrationStep
{
    public function getCreationTimestamp(): int{
        return 1711620000;
    }

    public function update(Connection $connection): void{
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS areanet_gmv (
                id BINARY(16) NOT NULL,
                year INT NOT NULL,
                gmv DECIMAL(17,4) NOT NULL,
                created_at DATETIME(3) NOT NULL,
                updated_at DATETIME(3) NULL,
                PRIMARY KEY (id),
                UNIQUE KEY year (year)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void{
        $connection->executeStatement('
            DROP TABLE IF EXISTS areanet_gmv;
        ');
    }
}
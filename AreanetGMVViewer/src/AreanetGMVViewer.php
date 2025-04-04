<?php declare(strict_types=1);

namespace AreanetGmvViewer;

use AreanetGmvViewer\Migration\Migration1743748124CreateGmvTable;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Doctrine\DBAL\Connection;

class AreanetGmvViewer extends Plugin{
    public function install(InstallContext $installContext): void{
        // Do stuff such as creating a new payment method
    }

    public function uninstall(UninstallContext $uninstallContext): void{
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        $connection = $this->container->get(Connection::class);

        $migration = new Migration1743748124CreateGmvTable();
        $migration->updateDestructive($connection);
    }
}

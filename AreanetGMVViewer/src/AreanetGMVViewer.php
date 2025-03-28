<?php declare(strict_types=1);

namespace AreanetGMVViewer;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AreanetGMVViewer extends Plugin{
    private ?SystemConfigService $systemConfigService = null;

    public function setContainer(?ContainerInterface $container): void{
        parent::setContainer($container);
        $this->systemConfigService = $container ? $container->get(SystemConfigService::class) : null;
    }

    public function install(InstallContext $context): void{
        if ($this->systemConfigService) {
            $this->systemConfigService->set('AreanetGMVViewer.config.my_setting', 'default_value');
        }
    }

    public function uninstall(UninstallContext $context): void{
        parent::uninstall($context);

        if ($context->keepUserData()) {
            return;
        }

        $migrationCollection = $context->getMigrationCollection();

        // Call migrateDestructiveInPlace() without any arguments
        $migrationCollection->migrateDestructiveInPlace();

        // If you were to use migrateDestructiveInSteps(), it might have a different signature.
        // Consult the Shopware developer documentation for the exact usage.
        // Example for migrateDestructiveInSteps (might also not require the context):
        // $migrationCollection->migrateDestructiveInSteps();
    }
}
<?php

namespace Goaffpro\AffiliateMarketing\Setup;

use Goaffpro\AffiliateMarketing\Helper\GoaffproApi;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @var GoaffproApi
     */
    private $api;

    /**
     * Uninstall constructor.
     * @param GoaffproApi $api
     */
    public function __construct(GoaffproApi $api)
    {
        $this->api = $api;
    }

    /**
     * @inheritDoc
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->api->moduleUninstall();
        $setup->endSetup();
    }
}

<?php

namespace Goaffpro\AffiliateMarketing\Setup;

use Goaffpro\AffiliateMarketing\Helper\GoaffproApi;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var ConfigInterface
     */
    protected $resourceConfig;
    /**
     * @var GoaffproApi
     */
    protected $api;
    /**
     * @var Random
     */
    protected $random;

    /**
     * InstallData constructor.
     * @param LoggerInterface $loggerInterface
     * @param ConfigInterface $resourceConfig
     * @param GoaffproApi $api
     * @param Random $random
     */
    public function __construct(
        LoggerInterface $loggerInterface,
        ConfigInterface $resourceConfig,
        GoaffproApi $api,
        Random $random
    ) {
        $this->logger = $loggerInterface;
        $this->resourceConfig = $resourceConfig;
        $this->api = $api;
        $this->random = $random;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $publicKey = $this->generateKey();
        $this->resourceConfig->saveConfig(
            'goaffpro/general/public_key',
            $publicKey,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            Store::DEFAULT_STORE_ID
        );
        $this->api->moduleInstalled($publicKey);
        $setup->endSetup();
    }

    /**
     * @return string
     */
    private function generateKey()
    {
        try {
            return $this->random->getRandomString(16);
        } catch (LocalizedException $e) {
            $this->logger->critical('Goaffpro install: ' . $e->getMessage());
        }
    }
}

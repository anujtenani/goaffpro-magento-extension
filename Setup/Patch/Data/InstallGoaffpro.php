<?php

namespace Goaffpro\AffiliateMarketing\Setup\Patch\Data;

use Goaffpro\AffiliateMarketing\Helper\GoaffproApi;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

class InstallGoaffpro implements DataPatchInterface
{
    private const CONFIG_PATH_PUBLIC_KEY = GoaffproApi::XML_PATH_PUBLIC_KEY;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var ConfigInterface
     */
    private $resourceConfig;

    /**
     * @var GoaffproApi
     */
    private $api;

    /**
     * @var Random
     */
    private $random;

    /**
     * @var State
     */
    private $state;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ConfigInterface $resourceConfig
     * @param GoaffproApi $api
     * @param Random $random
     * @param State $state
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ConfigInterface $resourceConfig,
        GoaffproApi $api,
        Random $random,
        State $state,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->resourceConfig = $resourceConfig;
        $this->api = $api;
        $this->random = $random;
        $this->state = $state;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        // Never regenerate the key for stores that already have the module installed.
        if ($this->getExistingPublicKey() !== null) {
            return $this;
        }

        $this->moduleDataSetup->startSetup();

        $publicKey = $this->generateKey();
        $this->resourceConfig->saveConfig(
            self::CONFIG_PATH_PUBLIC_KEY,
            $publicKey,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            Store::DEFAULT_STORE_ID
        );

        // The store name/currency lookups need a storefront area during setup.
        $this->state->emulateAreaCode(
            Area::AREA_FRONTEND,
            [$this->api, 'moduleInstalled'],
            [$publicKey]
        );

        $this->moduleDataSetup->endSetup();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return string|null
     */
    private function getExistingPublicKey()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $select = $connection->select()
            ->from($this->moduleDataSetup->getTable('core_config_data'), 'value')
            ->where('path = ?', self::CONFIG_PATH_PUBLIC_KEY)
            ->where('scope = ?', ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->where('scope_id = ?', Store::DEFAULT_STORE_ID);

        $value = $connection->fetchOne($select);

        return $value !== false && $value !== '' ? (string)$value : null;
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
            return '';
        }
    }
}

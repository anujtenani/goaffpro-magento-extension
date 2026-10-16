<?php

namespace Goaffpro\AffiliateMarketing\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'goaffpro/general/enabled';
    const XML_PATH_PUBLIC_KEY = 'goaffpro/general/public_key';

    /**
     * Keep in sync with the release tag (see RELEASE_GUIDE.md).
     */
    const PLUGIN_VERSION = '2.0.0';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Curl $curl
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Curl $curl
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
        $this->curl = $curl;
    }

    /**
     * Version of this plugin.
     *
     * @return string
     */
    public function getPluginVersion()
    {
        return self::PLUGIN_VERSION;
    }

    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getPublicKey($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PUBLIC_KEY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getStoreName($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'general/store_information/name',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getStoreCurrency()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }
}

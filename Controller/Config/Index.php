<?php

namespace Goaffpro\AffiliateMarketing\Controller\Config;

use Goaffpro\AffiliateMarketing\Helper\Data;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class Index implements ActionInterface
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @param Data $helper
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Data $helper,
        JsonFactory $resultJsonFactory
    ) {
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return Json
     */
    public function execute()
    {
        return $this->resultJsonFactory->create()->setData(
            [
                'goaffpro_public_token' => $this->helper->getPublicKey(),
                'store_name' => $this->helper->getStoreName(),
                'store_currency' => $this->helper->getStoreCurrency(),
            ]
        );
    }
}

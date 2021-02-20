<?php

namespace Goaffpro\AffiliateMarketing\Controller\Config;

use Goaffpro\AffiliateMarketing\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class Index extends Action
{
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param Data $helper
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Data $helper,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $response = [
            'goaffpro_public_token' => $this->helper->getPublicKey(),
            'store_name' => $this->helper->getStoreName(),
            'store_currency' => $this->helper->getStoreCurrency()
        ];
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}

<?php

namespace Goaffpro\AffiliateMarketing\Observer;

use Goaffpro\AffiliateMarketing\Helper\GoaffproApi;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class SalesOrderAfterSave implements ObserverInterface
{
    /**
     * @var GoaffproApi
     */
    protected $api;

    public function __construct(GoaffproApi $api)
    {
        $this->api = $api;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof Order) {
            $this->api->orderUpdated($order);
        }
        return $this;
    }
}

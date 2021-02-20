<?php

namespace Goaffpro\AffiliateMarketing\Block\Order;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }
}

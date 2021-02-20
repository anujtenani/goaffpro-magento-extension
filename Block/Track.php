<?php

namespace Goaffpro\AffiliateMarketing\Block;

use Goaffpro\AffiliateMarketing\Helper\Data;
use Magento\Framework\View\Element\Template;

class Track extends Template
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Track constructor.
     * @param Template\Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getPublicKey()
    {
        return $this->helper->getPublicKey();
    }

    protected function _toHtml()
    {
        if ($this->helper->isEnabled()) {
            return parent::_toHtml();
        }
        return false;
    }
}

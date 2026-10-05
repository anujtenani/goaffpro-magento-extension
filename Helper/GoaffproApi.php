<?php

namespace Goaffpro\AffiliateMarketing\Helper;

use Goaffpro\AffiliateMarketing\Model\OrderPayloadBuilder;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;

class GoaffproApi extends Data
{
    const GOAFFPRO_BASE_URL = 'https://api.goaffpro.com/magento/webhook/';

    /**
     * @var OrderPayloadBuilder
     */
    private $orderPayloadBuilder;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Curl $curl
     * @param OrderPayloadBuilder $orderPayloadBuilder
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Curl $curl,
        OrderPayloadBuilder $orderPayloadBuilder
    ) {
        $this->orderPayloadBuilder = $orderPayloadBuilder;
        parent::__construct($context, $storeManager, $curl);
    }

    /**
     * API request to Goaffpro API to confirm module installation
     * @param $publicKey
     * @return bool
     */
    public function moduleInstalled($publicKey)
    {
        $url = self::GOAFFPRO_BASE_URL . 'app.installed/' . $publicKey;
        $data = [
            'store_name' => $this->getStoreName(),
            'store_currency' => $this->getStoreCurrency()
        ];
        $this->curl->addHeader('Content-Type', 'application/json');
        $this->curl->post($url, json_encode($data));
        $result = $this->curl->getBody();
        return $result == 'OK';
    }

    /**
     * API request to Goaffpro to confirm module uninstall
     * @return bool
     */
    public function moduleUninstall()
    {
        $url = self::GOAFFPRO_BASE_URL . 'app.uninstalled/' . $this->getPublicKey();
        $data = [];
        $this->curl->addHeader('Content-Type', 'application/json');
        $this->curl->post($url, json_encode($data));
        $result = $this->curl->getBody();
        return $result == 'OK';
    }

    /**
     * API request to Goaffpro triggered on order update "sales_order_save_after"
     * @param Order $order
     * @return bool
     */
    public function orderUpdated(Order $order)
    {
        return $this->sendOrderWebhook('order.updated', $order);
    }

    /**
     * API request to Goaffpro triggered on place order "sales_order_place_after"
     * @param Order $order
     * @return bool
     */
    public function orderCreated(Order $order)
    {
        return $this->sendOrderWebhook('order.created', $order);
    }

    /**
     * Post the full order payload to Goaffpro for the given event.
     *
     * @param string $event
     * @param Order $order
     * @return bool
     */
    private function sendOrderWebhook($event, Order $order)
    {
        $url = self::GOAFFPRO_BASE_URL . $event . '/' . $this->getPublicKey();
        $this->curl->addHeader('Content-Type', 'application/json');
        $this->curl->post($url, json_encode($this->orderPayloadBuilder->build($order)));
        return $this->curl->getBody() == 'OK';
    }
}

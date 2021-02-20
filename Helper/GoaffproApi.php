<?php

namespace Goaffpro\AffiliateMarketing\Helper;

class GoaffproApi extends Data
{
    const GOAFFPRO_BASE_URL = 'https://api.goaffpro.com/magento/webhook/';

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
     * @param $orderId
     * @return bool
     */
    public function orderUpdated($orderId)
    {
        $url = self::GOAFFPRO_BASE_URL . 'order.updated/' . $this->getPublicKey();
        $data = ['id' => $orderId];
        $this->curl->addHeader('Content-Type', 'application/json');
        $this->curl->post($url, json_encode($data));
        $result = $this->curl->getBody();
        return $result == 'OK';
    }

    /**
     * API request to Goaffpro triggered on place order "sales_order_place_after"
     * @param $orderId
     * @return bool
     */
    public function orderCreated($orderId)
    {
        $url = self::GOAFFPRO_BASE_URL . 'order.created/' . $this->getPublicKey();
        $data = ['id' => $orderId];
        $this->curl->addHeader('Content-Type', 'application/json');
        $this->curl->post($url, json_encode($data));
        $result = $this->curl->getBody();
        return $result == 'OK';
    }
}

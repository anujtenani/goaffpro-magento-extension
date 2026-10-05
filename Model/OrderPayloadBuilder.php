<?php

namespace Goaffpro\AffiliateMarketing\Model;

use DateTimeImmutable;
use DateTimeZone;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * Builds the order payload sent to Goaffpro.
 */
class OrderPayloadBuilder
{
    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @param OrderCollectionFactory $orderCollectionFactory
     */
    public function __construct(OrderCollectionFactory $orderCollectionFactory)
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Build the Goaffpro payload for an order.
     *
     * @param Order $order
     * @return array
     */
    public function build(Order $order): array
    {
        $total = (float)$order->getGrandTotal();
        $shipping = (float)$order->getShippingAmount();
        $tax = (float)$order->getTaxAmount();

        return [
            'id' => $order->getId(),
            'increment_id' => $order->getIncrementId(),
            'number' => '#' . $order->getIncrementId(),
            'total' => $total,
            'subtotal' => $total - $shipping - $tax,
            'discount' => (float)$order->getDiscountAmount(),
            'tax' => $tax,
            'shipping' => $shipping,
            'currency' => $order->getOrderCurrencyCode(),
            'date' => $this->formatDate($order->getCreatedAt()),
            'customer' => $this->getCustomer($order),
            'coupons' => $this->getCoupons($order),
            'line_items' => $this->getLineItems($order),
            'raw' => $order->getData(),
        ];
    }

    /**
     * @param Order $order
     * @return array
     */
    private function getCustomer(Order $order): array
    {
        $address = $order->getBillingAddress() ?: $order->getShippingAddress();

        return [
            'first_name' => $order->getCustomerFirstname(),
            'last_name' => $order->getCustomerLastname(),
            'email' => $order->getCustomerEmail(),
            'phone' => $address ? $address->getTelephone() : null,
            'is_new_customer' => $this->isNewCustomer($order),
        ];
    }

    /**
     * @param Order $order
     * @return bool
     */
    private function isNewCustomer(Order $order): bool
    {
        $customerId = $order->getCustomerId();
        if (!$customerId || !$order->getId()) {
            return true;
        }

        $collection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('entity_id', ['lt' => $order->getId()]);

        return $collection->getSize() === 0;
    }

    /**
     * @param Order $order
     * @return array
     */
    private function getCoupons(Order $order): array
    {
        $couponCode = $order->getCouponCode();
        if (!$couponCode) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $couponCode))));
    }

    /**
     * @param Order $order
     * @return array
     */
    private function getLineItems(Order $order): array
    {
        $items = [];
        foreach ($order->getAllVisibleItems() as $item) {
            if ((float)$item->getQtyOrdered() <= 0) {
                continue;
            }

            $items[] = [
                'name' => $item->getName(),
                'quantity' => (float)$item->getQtyOrdered(),
                'price' => (float)$item->getPrice(),
                'sku' => $item->getSku(),
                'product_id' => $item->getProductId(),
                'tax' => (float)$item->getTaxAmount(),
                'discount' => (float)$item->getDiscountAmount(),
            ];
        }

        return $items;
    }

    /**
     * @param string|null $date
     * @return string|null
     */
    private function formatDate($date)
    {
        if (!$date) {
            return null;
        }

        return (new DateTimeImmutable($date, new DateTimeZone('UTC')))
            ->format('Y-m-d\TH:i:s.v\Z');
    }
}

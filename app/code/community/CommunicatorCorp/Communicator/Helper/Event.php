<?php

/*
 * CommunicatorCorp\Communicator
 *
 * Copyright © 2016 Rippleffect Studio Ltd
 * Rights reserved.
 *
 * PHP version 5.4+
 */

use CommunicatorCorp\Client\DateTime\DateTime;
use CommunicatorCorp\Client\EnumerationType\DataImportUpdateMethod;
use CommunicatorCorp\Client\EnumerationType\TriggeredDispatchMethod;
use CommunicatorCorp\Client\ObjectDefinition\ColumnMapping;
use CommunicatorCorp\Client\ObjectDefinition\DataImport;
use CommunicatorCorp\Client\ObjectDefinition\DataImportResponse;
use CommunicatorCorp\Client\ObjectDefinition\DataRecord;
use CommunicatorCorp\Client\ObjectDefinition\Subscription;
use CommunicatorCorp\Client\ObjectDefinition\TriggeredDispatch;

/**
 * Event helper.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Helper_Event extends CommunicatorCorp_Communicator_Helper_Abstract
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /** @const int The maximum number of recommended products per order. */
    const MAX_RECOMMENDATIONS = 3;

    /** @const string The configuration path to the subscriber event enabled state. */
    const XML_PATH_COMMUNICATOR_EVENT_SUBSCRIBER_ENABLED = 'communicator_event/subscriber/enabled';

    /** @const string The configuration path to the subscriber event dispatch. */
    const XML_PATH_COMMUNICATOR_EVENT_SUBSCRIBER_DISPATCH = 'communicator_event/subscriber/dispatch';

    /** @const string The configuration path to the customer event enabled state. */
    const XML_PATH_COMMUNICATOR_EVENT_CUSTOMER_ENABLED = 'communicator_event/customer/enabled';

    /** @const string The configuration path to the customer event dispatch. */
    const XML_PATH_COMMUNICATOR_EVENT_CUSTOMER_DISPATCH = 'communicator_event/customer/dispatch';

    /** @const string The configuration path to the product event enabled state. */
    const XML_PATH_COMMUNICATOR_EVENT_PRODUCT_ENABLED = 'communicator_event/product/enabled';

    /** @const string The configuration path to the order event enabled state. */
    const XML_PATH_COMMUNICATOR_EVENT_ORDER_ENABLED = 'communicator_event/order/enabled';

    /** @const string The configuration path to the order event dispatch. */
    const XML_PATH_COMMUNICATOR_EVENT_ORDER_DISPATCH = 'communicator_event/order/dispatch';

    /** @const string The configuration path to the order recommendation source. */
    const XML_PATH_COMMUNICATOR_EVENT_ORDER_RECOMMENDATION_SOURCE = 'communicator_event/order/recommendation_source';

    /** @const string The configuration path to the abandoned basket event enabled state. */
    const XML_PATH_COMMUNICATOR_EVENT_ABANDONED_BASKET_ENABLED = 'communicator_event/abandoned_basket/enabled';

    /** @const string The configuration path to the abandoned basket event interval. */
    const XML_PATH_COMMUNICATOR_EVENT_ABANDONED_BASKET_INTERVAL = 'communicator_event/abandoned_basket/interval';

    /** @const string The configuration path to the abandoned basket event dispatch. */
    const XML_PATH_COMMUNICATOR_EVENT_ABANDONED_BASKET_DISPATCH = 'communicator_event/abandoned_basket/dispatch';

    /** @const string The configuration path to the invoice event enabled state. */
    const XML_PATH_COMMUNICATOR_EVENT_INVOICE_ENABLED = 'communicator_event/invoice/enabled';

    /** @const string The configuration path to the invoice dispatch. */
    const XML_PATH_COMMUNICATOR_EVENT_INVOICE_DISPATCH = 'communicator_event/invoice/dispatch';

    /** @const string The configuration path to the shipment event enabled state. */
    const XML_PATH_COMMUNICATOR_EVENT_SHIPMENT_ENABLED = 'communicator_event/shipment/enabled';

    /** @const string The configuration path to the shipment dispatch. */
    const XML_PATH_COMMUNICATOR_EVENT_SHIPMENT_DISPATCH = 'communicator_event/shipment/dispatch';

    /**
     * Updates a customer subscription.
     *
     * @param Mage_Customer_Model_Customer $customer The customer.
     *
     * @return $this
     */
    public function updateCustomerSubscription(Mage_Customer_Model_Customer $customer)
    {
        $enabled = Mage::getStoreConfigFlag(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_ENABLED);

        if (!$enabled) {
            return $this;
        }

        $customerEmailAddress = $customer->getEmail();

        $mailingListId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_LIST_NEWSLETTER);

        $subscribedInCommunicator = $this->isContactSubscribed($customerEmailAddress, $mailingListId);

        /** @var Mage_Newsletter_Model_Subscriber $subscriber */
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customerEmailAddress);

        if (!$subscribedInCommunicator && $subscriber->getId()) {
            $subscriber->unsubscribe();
        }

        return $this;
    }

    /**
     * Upserts a subscriber to the Communicator API.
     *
     * @param Mage_Newsletter_Model_Subscriber $subscriber The subscriber.
     *
     * @return $this
     */
    public function upsertSubscriber(Mage_Newsletter_Model_Subscriber $subscriber)
    {
        $mailingListId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_LIST_NEWSLETTER);
        $clientTableId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_CONTACT);

        $disabled = !Mage::getStoreConfigFlag(self::XML_PATH_COMMUNICATOR_EVENT_CUSTOMER_ENABLED);

        if (empty($mailingListId) || empty($clientTableId) || $disabled) {
            return $this;
        }

        $columns = $this
            ->getMappingHelper()
            ->getSubscriberColumns($subscriber)
        ;

        $subscribed = ((int) $subscriber->getStatus()) === Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;

        $triggeredDispatches = array();
        $triggeredDispatchId = (int) Mage::getStoreConfig(self::XML_PATH_COMMUNICATOR_EVENT_SUBSCRIBER_DISPATCH);
        $dispatchEnabled = Mage::getStoreConfigFlag(self::XML_PATH_COMMUNICATOR_EVENT_SUBSCRIBER_ENABLED);

        if ($dispatchEnabled && $triggeredDispatchId && $subscribed) {
            $triggeredDispatches[] = $this->createTriggeredDispatch(
                $triggeredDispatchId,
                TriggeredDispatchMethod::ONLY_SEND_TO_NEW_SUBSCRIBERS
            );
        }

        $subscription = $this->createSubscription($mailingListId, $subscribed, false);
        $dataRecord   = $this->createDataRecord($columns, array($subscription));
        $dataImport   = $this->createDataImport($clientTableId, array($dataRecord), $triggeredDispatches);

        try {
            $this->dataImport($dataImport);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');

            $this->updateConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_SUBSCRIBER_ID, $subscriber->getId());
            $this->flushConfig();
        }

        return $this;
    }

    /**
     * Upserts multiple subscribers to Communicator in a single API call.
     *
     * @param Mage_Newsletter_Model_Resource_Subscriber_Collection $subscribers The subscriber collection.
     *
     * @return int The last synchronised subscriber ID.
     */
    public function upsertSubscribers(Mage_Newsletter_Model_Resource_Subscriber_Collection $subscribers)
    {
        $mailingListId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_LIST_NEWSLETTER);
        $clientTableId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_CONTACT);

        if (empty($mailingListId) || empty($clientTableId)) {
            return $this;
        }

        $subscriberId = 0;
        $dataRecords = array();
        /** @var Mage_Newsletter_Model_Subscriber $subscriber */
        foreach ($subscribers as $subscriber) {
            $columns = $this
                ->getMappingHelper()
                ->getSubscriberColumns($subscriber)
            ;

            $subscription = $this->createSubscription($mailingListId, $subscriber->isSubscribed());

            $dataRecords[] = $this->createDataRecord($columns, array($subscription));

            $subscriberId = $subscriber->getId();
        }

        $dataImport = $this->createDataImport($clientTableId, $dataRecords);

        try {
            $this->dataImport($dataImport);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
        }

        return $subscriberId;
    }

    /**
     * Upserts a customer to the Communicator API.
     *
     * @param Mage_Customer_Model_Customer $customer The customer.
     *
     * @return $this
     */
    public function upsertCustomer(Mage_Customer_Model_Customer $customer)
    {
        $clientTableId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_CONTACT);

        $disabled = !Mage::getStoreConfigFlag(self::XML_PATH_COMMUNICATOR_EVENT_CUSTOMER_ENABLED);

        if (empty($clientTableId) || $disabled) {
            return $this;
        }

        $columns = $this
            ->getMappingHelper()
            ->getCustomerColumns($customer)
        ;

        $lastOrderDate = $this->getLastOrderDateColumnMappingForCustomer($customer);
        if (!empty($lastOrderDate)) {
            $columns[] = $lastOrderDate;
        }

        $triggeredDispatches = array();
        $triggeredDispatchId = (int) Mage::getStoreConfig(self::XML_PATH_COMMUNICATOR_EVENT_CUSTOMER_DISPATCH);
        $dispatchEnabled = Mage::getStoreConfigFlag(self::XML_PATH_COMMUNICATOR_EVENT_CUSTOMER_ENABLED);
        $isCustomer = (bool) $customer->getId();

        if ($dispatchEnabled && $triggeredDispatchId && $isCustomer) {
            $triggeredDispatches[] = $this->createTriggeredDispatch(
                $triggeredDispatchId,
                TriggeredDispatchMethod::ONLY_SEND_TO_NEW_SUBSCRIBERS
            );
        }

        $transactionalMailingListId = Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_LIST_TRANSACTIONAL);
        $newsletterMailingListId = Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_LIST_NEWSLETTER);

        $subscriptions = array();
        $subscriptions[] = $this->createSubscription($transactionalMailingListId, true, false);

        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail());
        $subscribed = ((int) $subscriber->getStatus()) === Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;

        if ($subscribed) {
            $subscriptions[] = $this->createSubscription($newsletterMailingListId, true);
        }

        $dataRecord = $this->createDataRecord($columns, $subscriptions);
        $dataImport = $this->createDataImport($clientTableId, array($dataRecord), $triggeredDispatches);

        try {
            $this->dataImport($dataImport);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');

            $this->updateConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_CUSTOMER_ID, $customer->getId());
            $this->flushConfig();
        }

        return $this;
    }

    /**
     * Upserts multiple customers to Communicator in a single data import call.
     *
     * @param Mage_Customer_Model_Resource_Customer_Collection $customers The customer collection.
     *
     * @return int The ID of the most recently synchronised customer.
     */
    public function upsertCustomers(Mage_Customer_Model_Resource_Customer_Collection $customers)
    {
        $transactionalListId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_LIST_TRANSACTIONAL);
        $mailingListId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_LIST_NEWSLETTER);
        $clientTableId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_CONTACT);

        if (empty($mailingListId) || empty($clientTableId) || empty($transactionalListId)) {
            return $this;
        }

        $customerId = 0;
        $dataRecords = array();
        foreach ($customers as $customer) {
            $columns = $this
                ->getMappingHelper()
                ->getCustomerColumns($customer)
            ;

            $lastOrderDate = $this->getLastOrderDateColumnMappingForCustomer($customer);
            if (!empty($lastOrderDate)) {
                $columns[] = $lastOrderDate;
            }

            $subscriptions = array();
            $subscriptions[] = $this->createSubscription(
                $transactionalListId,
                true,
                false
            );

            $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail());
            $subscribed = ((int) $subscriber->getStatus()) === Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;

            if ($subscribed) {
                $subscriptions[] = $this->createSubscription($mailingListId, true);
            }

            $dataRecords[] = $this->createDataRecord($columns, $subscriptions);

            $customerId = $customer->getId();
        }

        $dataImport = $this->createDataImport($clientTableId, $dataRecords);

        try {
            $this->dataImport($dataImport);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
        }

        return $customerId;
    }

    /**
     * Upserts multiple products to Communicator in a single data import call.
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $products The product collection.
     *
     * @return int The ID of the most recently synchronised product.
     */
    public function upsertProducts(Mage_Catalog_Model_Resource_Product_Collection $products)
    {
        $clientTableId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_PRODUCT);

        if (empty($clientTableId)) {
            return $this;
        }

        $productId = 0;
        $dataRecords = array();

        foreach ($products as $product) {
            $productId = $product->getId();
            $product = Mage::getModel('catalog/product')->load($productId);

            $columns = $this
                ->getMappingHelper()
                ->getProductColumns($product)
            ;

            $dataRecords[] = $this->createDataRecord($columns);
        }

        $dataImport = $this->createDataImport($clientTableId, $dataRecords);

        try {
            $this->dataImport($dataImport);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
        }

        return $productId;
    }

    /**
     * Upserts a product to the Communicator API.
     *
     * @param Mage_Catalog_Model_Product $product The product.
     *
     * @return $this
     */
    public function upsertProduct(Mage_Catalog_Model_Product $product)
    {
        $clientTableId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_PRODUCT);
        $disabled = !Mage::getStoreConfigFlag(self::XML_PATH_COMMUNICATOR_EVENT_PRODUCT_ENABLED);

        if ($disabled || empty($clientTableId)) {
            return $this;
        }

        $columns = $this
            ->getMappingHelper()
            ->getProductColumns($product)
        ;

        $dataRecord = $this->createDataRecord($columns);
        $dataImport = $this->createDataImport($clientTableId, array($dataRecord));

        try {
            $this->dataImport($dataImport);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');

            $this->updateConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_PRODUCT_ID, $product->getId());
            $this->flushConfig();
        }

        return $this;
    }

    /**
     * Upserts a quote into the Communicator orders data table.
     *
     * @param Mage_Sales_Model_Quote $quote The quote.
     *
     * @return $this
     */
    public function upsertQuote(Mage_Sales_Model_Quote $quote)
    {
        if ($quote->getCustomerIsGuest()) {
            return $this;
        }

        $quoteMailingListId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_LIST_ABANDONED_BASKET);
        $quoteClientTableId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_ORDER);

        if (empty($quoteClientTableId) || empty($quoteMailingListId)) {
            return $this;
        }

        $this->upsertCustomer($quote->getCustomer());

        $disabled = !Mage::getStoreConfigFlag(self::XML_PATH_COMMUNICATOR_EVENT_ABANDONED_BASKET_ENABLED);

        if (empty($quoteMailingListId) || empty($quoteClientTableId) || $disabled) {
            return $this;
        }

        $columns = $this
            ->getMappingHelper()
            ->getQuoteColumns($quote)
        ;

        $checkoutUrlMapping = (new ColumnMapping)
            ->setColumnId((int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Mapping::XML_PATH_COMMUNICATOR_MAPPING_ORDER_CHECKOUT_URL))
            ->setValue(Mage::helper('checkout/url')->getCheckoutUrl())
        ;

        // Quotes have a status of pending, since this is required to trigger
        // the abandoned basket email.
        $statusMapping = (new ColumnMapping)
            ->setColumnId((int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Mapping::XML_PATH_COMMUNICATOR_MAPPING_ORDER_STATUS))
            ->setValue('pending')
        ;

        array_push($columns, $checkoutUrlMapping, $statusMapping);

        $dataRecord = $this->createDataRecord($columns);
        $dataImport = $this->createDataImport($quoteClientTableId, array($dataRecord));

        try {
            $this->dataImport($dataImport);
            $this->upsertQuoteItems($quote);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
        }

        return $this;
    }

    /**
     * Upserts quote items to the Communicator API.
     *
     * @param Mage_Sales_Model_Quote $quote The quote.
     *
     * @return $this
     */
    public function upsertQuoteItems(Mage_Sales_Model_Quote $quote)
    {
        $quoteItemClientTableId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_ORDER_ITEM);

        if (empty($quoteItemClientTableId)) {
            return $this;
        }

        $dataHelper = $this->getDataHelper();
        $mappingHelper = $this->getMappingHelper();

        $email = $mappingHelper->createColumnMapping(
            (int) Mage::getStoreConfig($mappingHelper::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_EMAIL),
            $quote->getCustomerEmail()
        );

        $quoteId = $mappingHelper->createColumnMapping(
            (int) Mage::getStoreConfig($mappingHelper::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_QUOTE_ID),
            $quote->getId()
        );

        $quoteItemIdColumnId = (int) Mage::getStoreConfig($mappingHelper::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_ORDER_ITEM_ID);
        $quoteMailingListId  = (int) Mage::getStoreConfig($dataHelper::XML_PATH_COMMUNICATOR_LIST_ABANDONED_BASKET);

        $subscriptions = array();
        $subscriptions[] = $this->createSubscription($quoteMailingListId, true);

        /** @var Mage_Newsletter_Model_Subscriber $subscriber */
        $subscriber = Mage::getModel('newsletter/subscriber')
            ->loadByEmail($quote->getCustomerEmail());

        if ($subscriber->getId() && $subscriber->isSubscribed()) {
            $mailingListId = (int) Mage::getStoreConfig($dataHelper::XML_PATH_COMMUNICATOR_LIST_NEWSLETTER);
            array_push($subscriptions, $this->createSubscription($mailingListId, true, true));
        }

        $dataRecords = array();
        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        foreach ($quote->getAllItems() as $quoteItem) {
            if ('configurable' === $quoteItem->getProductType()) {
                continue;
            }

            $columns = $this
                ->getMappingHelper()
                ->getQuoteItemColumns($quoteItem)
            ;

            $quoteItemId = $mappingHelper->createColumnMapping($quoteItemIdColumnId, $quoteItem->getId());

            // Clone is used to prevent our request from using internal references.
            array_push($columns, clone $email, clone $quoteId, $quoteItemId);

            // Similarly, we want to avoid internal references for subscriptions
            $copiedSubscriptions = array_map(function (Subscription $subscription) {
                return clone $subscription;
            }, $subscriptions);

            $dataRecords[] = $this->createDataRecord($columns, $copiedSubscriptions);
        }

        $triggeredDispatches = array();
        $triggeredDispatchId = (int) Mage::getStoreConfig(self::XML_PATH_COMMUNICATOR_EVENT_ABANDONED_BASKET_DISPATCH);

        if ($triggeredDispatchId) {
            $triggeredDispatches[] = $this->createTriggeredDispatch(
                $triggeredDispatchId,
                TriggeredDispatchMethod::SEND_TO_ALL_SUBSCRIBERS
            );
        }

        $dataImport = $this->createDataImport(
            $quoteItemClientTableId,
            $dataRecords,
            $triggeredDispatches,
            DataImportUpdateMethod::UPSERT
        );

        try {
            $this->dataImport($dataImport);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
        }

        return $this;
    }

    /**
     * Upserts multiple orders into Communicator in a single API call.
     *
     * @param Mage_Sales_Model_Resource_Order_Collection $orders The orders.
     *
     * @return int
     */
    public function upsertOrders(Mage_Sales_Model_Resource_Order_Collection $orders)
    {
        $clientTableId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_ORDER);

        if (empty($clientTableId)) {
            return $this;
        }

        $orderId = 0;
        $dataRecords = array();

        /** @var Mage_Sales_Model_Order $order */
        foreach ($orders as $order) {
            $orderId = $order->getId();

            if ($order->getCustomerIsGuest()) {
                /** @var Mage_Customer_Model_Customer $customer */
                $customer = Mage::getModel('customer/customer');
                $customer->setData(array(
                    'email'      => $order->getCustomerEmail(),
                    'firstname'  => $order->getCustomerFirstname(),
                    'middlename' => $order->getCustomerMiddlename(),
                    'lastname'   => $order->getCustomerLastname(),
                ));

                $this->upsertCustomer($customer);
            }

            $paymentMethod = $order->getPayment()->getMethod();
            if (in_array($paymentMethod, ['cashondelivery', 'checkmo', 'purchaseorder'])) {
                // Calling setState throws an exception since the state is internal to Magento.
                // Since the order is not saved after this, we only modify the state in memory to prevent
                // emails being sent out by Communicator for stores that do not manage their state
                // correctly and use either cash on delivery, check / money orders, or purchase orders.
                $order->setData('state', 'complete');
            }

            $columns = $this
                ->getMappingHelper()
                ->getOrderColumns($order)
            ;

            $columns = array_merge($columns, $this->getRecommendedProducts($order));

            $dataRecords[] = $this->createDataRecord($columns);
        }

        $dataImport = $this->createDataImport($clientTableId, $dataRecords);

        try {
            $this->dataImport($dataImport);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
        }

        foreach ($orders as $order) {
            $this->upsertOrderItems($order);
        }

        return $orderId;
    }

    /**
     * Upserts an order to the Communicator API.
     *
     * @param Mage_Sales_Model_Order          $order               The order.
     * @param int|null                        $triggeredDispatchId The triggered dispatch ID.
     * @param Mage_Sales_Model_Order_Shipment $shipment            The shipment.
     *
     * @return $this
     */
    public function upsertOrder(Mage_Sales_Model_Order $order, $triggeredDispatchId = null, Mage_Sales_Model_Order_Shipment $shipment = null)
    {
        $orderClientTableId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_ORDER);
        $disabled = !Mage::getStoreConfigFlag(self::XML_PATH_COMMUNICATOR_EVENT_ORDER_ENABLED);

        if (empty($orderClientTableId) || $disabled) {
            return $this;
        }

        if ($order->getCustomerIsGuest()) {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = Mage::getModel('customer/customer');
            $customer->setData(array(
                'email'      => $order->getCustomerEmail(),
                'firstname'  => $order->getCustomerFirstname(),
                'middlename' => $order->getCustomerMiddlename(),
                'lastname'   => $order->getCustomerLastname(),
            ));
        } else {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        }

        $this->upsertCustomer($customer);

        $columns = $this
            ->getMappingHelper()
            ->getOrderColumns($order)
        ;

        $columns = array_merge($columns, $this->getRecommendedProducts($order));

        $dataRecord = $this->createDataRecord($columns);
        $dataImport = $this->createDataImport($orderClientTableId, array($dataRecord));

        try {
            $this->dataImport($dataImport);
            $this->upsertOrderItems($order, $triggeredDispatchId, $shipment);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');

            $this->updateConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_ORDER_ID, $order->getId());
            $this->flushConfig();
        }

        return $this;
    }

    /**
     * Upserts order items to the Communicator API.
     *
     * @param Mage_Sales_Model_Order          $order               The order.
     * @param int|null                        $triggeredDispatchId The triggered dispatch ID.
     * @param Mage_Sales_Model_Order_Shipment $shipment            The shipment.
     *
     * @return $this
     */
    private function upsertOrderItems(Mage_Sales_Model_Order $order, $triggeredDispatchId = null, Mage_Sales_Model_Order_Shipment $shipment = null)
    {
        $orderItemClientTableId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_ORDER_ITEM);
        $shipmentDispatch = Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_SHIPMENT_DISPATCH);

        if (empty($orderItemClientTableId)) {
            return $this;
        }

        $dataHelper = $this->getDataHelper();
        $mappingHelper = $this->getMappingHelper();

        $email = $mappingHelper->createColumnMapping(
            (int) Mage::getStoreConfig($mappingHelper::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_EMAIL),
            $order->getCustomerEmail()
        );

        $quoteId = $mappingHelper->createColumnMapping(
            (int) Mage::getStoreConfig($mappingHelper::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_QUOTE_ID),
            $order->getQuoteId()
        );

        $orderMailingListId  = (int) Mage::getStoreConfig($dataHelper::XML_PATH_COMMUNICATOR_LIST_TRANSACTIONAL);
        $orderItemIdColumnId = (int) Mage::getStoreConfig($mappingHelper::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_ORDER_ITEM_ID);

        $subscriptions = array();
        $subscriptions[] = $this->createSubscription($orderMailingListId, true, false);

        /** @var Mage_Newsletter_Model_Subscriber $subscriber */
        $subscriber = Mage::getModel('newsletter/subscriber')
            ->loadByEmail($order->getCustomerEmail());

        if ($subscriber->getId() && $subscriber->isSubscribed()) {
            $mailingListId = (int) Mage::getStoreConfig($dataHelper::XML_PATH_COMMUNICATOR_LIST_NEWSLETTER);
            array_push($subscriptions, $this->createSubscription($mailingListId, true, true));
        }

        $shipmentOrderIds = array();
        $dataRecords = array();

        if ($shipment !== null) {
            $shipmentItems = $shipment->getAllItems();
            /** @var Mage_Sales_Model_Order_Shipment_Item $shipmentItem */
            foreach ($shipmentItems as $shipmentItem) {
                $shipmentOrderIds[] = $shipmentItem->getOrderItemId();
            }
        }

        $orderItems = $order->getAllItems();

        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($orderItems as $orderItem) {
            if ('configurable' === $orderItem->getProductType()) {
                continue;
            }

            $orderItemId = $orderItem->getParentItem() !== null ? $orderItem->getParentItem()->getId() : $orderItem->getId();
            $shippingId = null;

            /** @var Mage_Sales_Model_Resource_Order_Shipment_Item_Collection $collection */
            $collection = Mage::getModel('sales/order_shipment_item')->getCollection()
                ->join(['shipment' => 'sales/shipment'], 'main_table.parent_id = shipment.entity_id')
                ->addFieldToFilter('main_table.order_item_id', $orderItemId)
            ;

            /** @var Mage_Sales_Model_Order_Shipment_Item $shipmentItem */
            $shipmentItem = $collection->getFirstItem();

            if (!$shipmentItem->isEmpty()) {
                $shippingId = $this->getMappingHelper()->createColumnMapping(
                    (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Mapping::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_SHIPPING_ID),
                    $shipmentItem->getIncrementId()
                );
            }

            // Prevent sending to CC not shipped order item on shipment dispatch
            if (($shippingId === null || !in_array($orderItemId, $shipmentOrderIds)) && $triggeredDispatchId === $shipmentDispatch) {
                continue;
            }

            $columns = $this
                ->getMappingHelper()
                ->getOrderItemColumns($orderItem)
            ;

            $orderItemId = $mappingHelper->createColumnMapping($orderItemIdColumnId, $orderItem->getId());

            // Clone is used to prevent our request from using internal references.
            array_push($columns, clone $email, clone $quoteId, $orderItemId);
            if ($shippingId !== null) {
                array_push($columns, clone $shippingId);
            }

            // Similarly, we want to avoid internal references for subscriptions
            $copiedSubscriptions = array_map(function (Subscription $subscription) {
                return clone $subscription;
            }, $subscriptions);

            $dataRecords[] = $this->createDataRecord($columns, $copiedSubscriptions);
        }

        $triggeredDispatches = array();

        if (null === $triggeredDispatchId) {
            $triggeredDispatchId = (int) Mage::getStoreConfig(self::XML_PATH_COMMUNICATOR_EVENT_ORDER_DISPATCH);
            $dispatchEnabled = in_array($order->getState(), array('new', 'processing')) && Mage::getStoreConfigFlag(self::XML_PATH_COMMUNICATOR_EVENT_ORDER_ENABLED);
            $triggeredDispatchGiven = false;
        } else {
            $dispatchEnabled = true;
            $triggeredDispatchGiven = true;
        }

        if ($dispatchEnabled && $triggeredDispatchId && ($triggeredDispatchGiven || !$order->getDispatchTriggered())) {
            $triggeredDispatches[] = $this->createTriggeredDispatch(
                $triggeredDispatchId,
                TriggeredDispatchMethod::SEND_TO_ALL_SUBSCRIBERS
            );

            // If no triggered dispatch is given, this is an order update event.
            if (!$triggeredDispatchGiven && !$order->getDispatchTriggered()) {
                $order->setDispatchTriggered(true);
                $order->getResource()->saveAttribute($order, 'dispatch_triggered');
            }
        }

        $dataImport = $this->createDataImport(
            $orderItemClientTableId,
            $dataRecords,
            $triggeredDispatches,
            DataImportUpdateMethod::UPSERT
        );

        try {
            $this->dataImport($dataImport);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
        }

        return $this;
    }

    /**
     * Upserts a shipment to the Communicator API.
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment Order shipment
     *
     * @return $this
     */
    public function upsertShipment(Mage_Sales_Model_Order_Shipment $shipment)
    {
        $shippingClientTableId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_SHIPPING);
        $disabled = !Mage::getStoreConfigFlag(self::XML_PATH_COMMUNICATOR_EVENT_SHIPMENT_ENABLED);

        if (empty($shippingClientTableId) || $disabled) {
            return $this;
        }

        $tracks = $shipment->getAllTracks();
        $dataRecord = [];

        /** @var Mage_Sales_Model_Order_Shipment_Track $track */
        foreach ($tracks as $track) {
            $track->setEmail($shipment->getShippingAddress()->getEmail());
            $track->setOrderIncrementId($shipment->getOrder()->getIncrementId());
            $track->setShipmentIncrementId($shipment->getIncrementId());
            $columns = $this
                ->getMappingHelper()
                ->getShipmentTrackColumns($track)
            ;
            $dataRecord[] = $this->createDataRecord($columns);
        }

        $dataImport = $this->createDataImport($shippingClientTableId, $dataRecord);

        try {
            $this->dataImport($dataImport);
            $this->upsertOrderItems($shipment->getOrder());
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
            $this->updateConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_SHIPMENT_ID, $shipment->getId());
            $this->flushConfig();
        }

        return $this;
    }

    /**
     * Upserts multiple shipments to Communicator in a single data import call.
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $shipments Shipment collection.
     *
     * @return int The ID of the most recently synchronised shipment.
     */
    public function upsertShipments(Mage_Sales_Model_Resource_Order_Shipment_Collection $shipments)
    {
        $clientShipmentId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_SHIPPING);

        if (empty($clientShipmentId)) {
            return $this;
        }

        $shipmentId = 0;
        $dataRecords = [];

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        foreach ($shipments as $shipment) {
            $shipmentId = $shipment->getId();
            $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
            $tracks = $shipment->getAllTracks();

            /** @var Mage_Sales_Model_Order_Shipment_Track $track */
            foreach ($tracks as $track) {
                $track->setEmail($shipment->getShippingAddress()->getEmail());
                $track->setOrderIncrementId($shipment->getOrder()->getIncrementId());
                $track->setShipmentIncrementId($shipment->getIncrementId());
                $columns = $this
                    ->getMappingHelper()
                    ->getShipmentTrackColumns($track)
                ;
                $dataRecords[] = $this->createDataRecord($columns);
            }
        }

        $dataImport = $this->createDataImport($clientShipmentId, $dataRecords);

        try {
            $this->dataImport($dataImport);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
        }

        foreach ($shipments as $shipment) {
            $this->upsertOrderItems($shipment->getOrder());
        }

        return $shipmentId;
    }

    /**
     * Creates a Communicator `Subscription` object.
     *
     * @param int  $mailingListId              The mailing list ID.
     * @param bool $subscribed                 True if subscribed, false otherwise.
     * @param bool $honourExistingUnsubscribes True to honour existing unsubscribes, false otherwise.
     *
     * @return Subscription
     */
    protected function createSubscription($mailingListId, $subscribed = true, $honourExistingUnsubscribes = true)
    {
        return (new Subscription)
            ->setMailingListId($mailingListId)
            ->setSubscribed($subscribed)
            ->setHonourExistingUnsubscribes($honourExistingUnsubscribes)
        ;
    }

    /**
     * Creates a Communicator `DataRecord` object.
     *
     * @param ColumnMapping[] $columnMappings         The column mappings.
     * @param Subscription[]  $subscriptions          The subscriptions.
     * @param bool            $isGloballyUnsubscribed True to unsubscribe globally.
     *
     * @return DataRecord
     */
    protected function createDataRecord(array $columnMappings, array $subscriptions = array(), $isGloballyUnsubscribed = false)
    {
        return (new DataRecord)
            ->setColumnMappings($columnMappings)
            ->setSubscriptions($subscriptions)
            ->setIsGloballyUnsubscribed($isGloballyUnsubscribed)
        ;
    }

    /**
     * Creates a Communicator `TriggeredDispatch` object.
     *
     * @param int    $triggeredDispatchId     The triggered dispatch ID.
     * @param string $triggeredDispatchMethod The triggered dispatch method.
     *
     * @return TriggeredDispatch
     */
    protected function createTriggeredDispatch($triggeredDispatchId, $triggeredDispatchMethod)
    {
        $triggeredDispatchMethod = new TriggeredDispatchMethod($triggeredDispatchMethod);

        return (new TriggeredDispatch)
            ->setDispatchId($triggeredDispatchId)
            ->setTriggeredDispatchMethod($triggeredDispatchMethod)
        ;
    }

    /**
     * Creates a Communicator `DataImport` object.
     *
     * By default, the data import will be configured to attempt to upsert the
     * given records, in some situations it may be more appropriate to attempt
     * to insert the records and catch any exceptions thrown by the client.
     *
     * @param int                 $clientTableId          The ID of the client table.
     * @param DataRecord[]        $records                The records to import.
     * @param TriggeredDispatch[] $triggeredDispatches    The triggered dispatches.
     * @param string              $dataImportUpdateMethod The data import update method.
     *
     * @return DataImport
     */
    protected function createDataImport(
        $clientTableId,
        array $records,
        array $triggeredDispatches = array(),
        $dataImportUpdateMethod = DataImportUpdateMethod::UPSERT
    ) {
        return (new DataImport)
            ->setClientTableId($clientTableId)
            ->setRecords($records)
            ->setTriggeredDispatches($triggeredDispatches)
            ->setType(new DataImportUpdateMethod($dataImportUpdateMethod))
        ;
    }

    /**
     * Returns a boolean indicative of whether an email address is subscribed to a mailing list.
     *
     * @param string $emailAddress  The email address of the contact.
     * @param int    $mailingListId The mailing list ID.
     *
     * @return bool
     */
    protected function isContactSubscribed($emailAddress, $mailingListId)
    {
        try {
            $subscriptionInfo = $this
                ->getDataHelper()
                ->getDataService()
                ->getContactSubscription($emailAddress, $mailingListId)
            ;
        } catch (Exception $e) {
            return false;
        }

        return $subscriptionInfo->getIsSubscribed();
    }

    /**
     * Imports data into the Communicator API.
     *
     * @param DataImport $dataImport The data import.
     *
     * @return DataImportResponse
     */
    protected function dataImport(DataImport $dataImport)
    {
        return $this
            ->getDataHelper()
            ->getDataService()
            ->dataImporter($dataImport)
        ;
    }

    /**
     * Returns a `ColumnMapping` for the last order date of a given customer.
     *
     * @param Mage_Customer_Model_Customer $customer The customer.
     *
     * @return ColumnMapping
     */
    private function getLastOrderDateColumnMappingForCustomer(Mage_Customer_Model_Customer $customer)
    {
        $lastOrderDateMapping = CommunicatorCorp_Communicator_Helper_Mapping::XML_PATH_COMMUNICATOR_MAPPING_CONTACT_LAST_ORDER_DATE;
        $lastOrderDateColumnId = Mage::getStoreConfig($lastOrderDateMapping);

        if (empty($lastOrderDateColumnId)) {
            return null;
        }

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('created_at')
            ->addFieldToFilter('customer_id', $customer->getId())
            ->addAttributeToSort('created_at', 'DESC')
            ->setPageSize(1)
        ;

        $lastOrderDate = $orders
            ->getFirstItem()
            ->getCreatedAt()
        ;

        if (null === $lastOrderDate) {
            return null;
        }

        return $this
            ->getMappingHelper()
            ->createColumnMapping(
                (int) $lastOrderDateColumnId,
                (new DateTime($lastOrderDate))->format('d/m/Y H:i:s')
            )
        ;
    }

    /**
     * Returns the recommended products for a given order.
     *
     * @param Mage_Sales_Model_Order $order The order.
     *
     * @return array
     */
    private function getRecommendedProducts(Mage_Sales_Model_Order $order)
    {
        $columns = array();
        $orderItems = $order->getAllItems();

        $recommendationSource = Mage::getStoreConfig(self::XML_PATH_COMMUNICATOR_EVENT_ORDER_RECOMMENDATION_SOURCE);

        foreach ($orderItems as $orderItem) {
            /** @var Mage_Catalog_Model_Product $product */
            $product = Mage::getModel('catalog/product')->load($orderItem->getProductId());

            if ('configurable' === $product->getTypeId()) {
                continue;
            }

            $recommendedProductIds = call_user_func([$product, $recommendationSource]);

            if (empty($recommendedProductIds)) {
                continue;
            }

            foreach ($recommendedProductIds as $i => $recommendedProductId) {
                $constantName = sprintf(
                    'CommunicatorCorp_Communicator_Helper_Mapping::XML_PATH_COMMUNICATOR_MAPPING_ORDER_RECOMMENDATION_%d',
                    1 + $i
                );

                $recommendationColumnMapping = $this
                    ->getMappingHelper()
                    ->createColumnMapping(
                        Mage::getStoreConfig(constant($constantName)),
                        $recommendedProductId
                    )
                ;

                array_push($columns, $recommendationColumnMapping);
            }

            if (self::MAX_RECOMMENDATIONS === $columns) {
                break;
            }
        }

        return $columns;
    }

    /**
     * Updates a configuration path with a given value.
     *
     * @param string $path  The configuration path.
     * @param mixed  $value The configuration value.
     *
     * @return void
     */
    private function updateConfig($path, $value)
    {
        $config = new Mage_Core_Model_Config();
        $config->saveConfig($path, $value);
    }

    /**
     * Flush the configuration cache.
     *
     * @return $this
     */
    private function flushConfig()
    {
        Mage::app()->getCacheInstance()->cleanType('config');

        return $this;
    }
}

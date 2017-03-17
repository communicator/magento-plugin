<?php

/*
 * CommunicatorCorp\Communicator
 *
 * Copyright © 2016 Rippleffect Studio Ltd
 * Rights reserved.
 *
 * PHP version 5.4+
 */

/**
 * Cron model.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Cron
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /**
     * Inserts existing data into Communicator.
     *
     * @return void
     */
    public function insertExisting()
    {
        $enabled = $this->getDataHelper()->isEnabled() &&
            Mage::getStoreConfigFlag(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_ENABLE_CRON_INSERT_EXISTING_DATA);

        if (!$enabled) {
            return;
        }

        Mage::app()->getCacheInstance()->cleanType('config');

        $this->insertExistingSubscribers();
        $this->insertExistingProducts();
        $this->insertExistingCustomers();
        $this->insertExistingOrders();
        $this->insertExistingShipments();
    }

    /**
     * Inserts abandoned orders into Communicator.
     *
     * @return void
     */
    public function insertAbandonedOrders()
    {
        $enabled = Mage::getStoreConfigFlag(CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_ABANDONED_BASKET_ENABLED) &&
                   Mage::getStoreConfigFlag(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_ENABLED);

        if (!$enabled) {
            return;
        }

        $configuredInterval = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_ABANDONED_BASKET_INTERVAL);

        $startUpdatedAt = date(Varien_Date::DATETIME_PHP_FORMAT, time() - (($configuredInterval + 5) * 60));
        $endUpdatedAt   = date(Varien_Date::DATETIME_PHP_FORMAT, time() - ($configuredInterval * 60));

        /** @var Mage_Sales_Model_Resource_Quote_Collection $quotes */
        $quotes = Mage::getModel('sales/quote')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_active', array('eq' => 1))
            ->addFieldToFilter('customer_id', array('notnull' => true))
            ->addFieldToFilter('converted_at', array('null' => true))
            ->addFieldToFilter('updated_at', array('gteq' => $startUpdatedAt))
            ->addFieldToFilter('updated_at', array('lteq' => $endUpdatedAt))
        ;

        foreach ($quotes as $quote) {
            try {
                $this
                    ->getEventHelper()
                    ->upsertQuote($quote)
                ;
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'communicator.log');
            }
        }
    }

    /**
     * Inserts existing subscribers to Communicator.
     *
     * @return void
     */
    private function insertExistingSubscribers()
    {
        $enabled = $this->isExistingSynchronisationEnabled();

        if (!$enabled) {
            return;
        }

        $lastSubscriberId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SUBSCRIBER_ID);
        $stopSubscriberId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_SUBSCRIBER_ID);

        if ($lastSubscriberId === $stopSubscriberId) {
            return;
        }

        /** @var Mage_Newsletter_Model_Resource_Subscriber_Collection $subscribers */
        $subscribers = Mage::getModel('newsletter/subscriber')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('subscriber_id', array('gt'   => $lastSubscriberId))
            ->addFieldToFilter('subscriber_id', array('lteq' => $stopSubscriberId))
        ;

        $customSchedule = Mage::getStoreConfig(CommunicatorCorp_Communicator_Model_Observer_Config::XML_PATH_COMMUNICATOR_GENERAL_CUSTOM_CRON_ENABLED);

        if (!$customSchedule) {
            $subscribers->setPageSize(50);
        }

        $lastSubscriberId = $this
            ->getEventHelper()
            ->upsertSubscribers($subscribers)
        ;

        $this->saveConfig(
            CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SUBSCRIBER_ID,
            $lastSubscriberId
        );
    }

    /**
     * Inserts existing customers into Communicator.
     *
     * @return void
     */
    private function insertExistingCustomers()
    {
        $enabled = $this->isExistingSynchronisationEnabled();

        if (!$enabled) {
            return;
        }

        $lastProductId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_PRODUCT_ID);
        $stopProductId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_PRODUCT_ID);

        // Only start synchronising customers once the synchronisation of products is completed.
        if ($lastProductId !== $stopProductId) {
            return;
        }

        $lastCustomerId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_CUSTOMER_ID);
        $stopCustomerId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_CUSTOMER_ID);

        if ($lastCustomerId === $stopCustomerId) {
            return;
        }

        /** @var Mage_Customer_Model_Resource_Customer_Collection $customers */
        $customers = Mage::getModel('customer/customer')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', array('gt'   => $lastCustomerId))
            ->addFieldToFilter('entity_id', array('lteq' => $stopCustomerId))
        ;

        $customSchedule = Mage::getStoreConfig(CommunicatorCorp_Communicator_Model_Observer_Config::XML_PATH_COMMUNICATOR_GENERAL_CUSTOM_CRON_ENABLED);

        if (!$customSchedule) {
            $customers->setPageSize(50);
        }

        $lastCustomerId = $this
            ->getEventHelper()
            ->upsertCustomers($customers)
        ;

        $this->saveConfig(
            CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_CUSTOMER_ID,
            $lastCustomerId
        );
    }

    /**
     * Inserts existing products into Communicator.
     *
     * @return void
     */
    private function insertExistingProducts()
    {
        $enabled = $this->isExistingSynchronisationEnabled();

        if (!$enabled) {
            return;
        }

        $lastProductId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_PRODUCT_ID);
        $stopProductId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_PRODUCT_ID);

        if ($lastProductId === $stopProductId) {
            return;
        }

        /** @var Mage_Catalog_Model_Resource_Product_Collection $products */
        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', array('gt'   => $lastProductId))
            ->addFieldToFilter('entity_id', array('lteq' => $stopProductId))
        ;

        $customSchedule = Mage::getStoreConfigFlag(CommunicatorCorp_Communicator_Model_Observer_Config::XML_PATH_COMMUNICATOR_GENERAL_CUSTOM_CRON_ENABLED);

        if (!$customSchedule) {
            $products->setPageSize(50);
        }

        $lastProductId = $this
            ->getEventHelper()
            ->upsertProducts($products)
        ;

        $this->saveConfig(
            CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_PRODUCT_ID,
            $lastProductId
        );
    }

    /**
     * Inserts existing orders into Communicator.
     *
     * @return void
     */
    private function insertExistingOrders()
    {
        $enabled = $this->isExistingSynchronisationEnabled();

        if (!$enabled) {
            return;
        }

        $lastCustomerId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_CUSTOMER_ID);
        $stopCustomerId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_CUSTOMER_ID);

        // Only start synchronising orders once the synchronisation of customers is completed.
        if ($lastCustomerId !== $stopCustomerId) {
            return;
        }

        $lastOrderId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_ORDER_ID);
        $stopOrderId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_ORDER_ID);

        if ($lastOrderId === $stopOrderId) {
            return;
        }

        $orders = Mage::getModel('sales/order')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', array('gt'   => $lastOrderId))
            ->addFieldToFilter('entity_id', array('lteq' => $stopOrderId))
        ;

        $customSchedule = Mage::getStoreConfigFlag(CommunicatorCorp_Communicator_Model_Observer_Config::XML_PATH_COMMUNICATOR_GENERAL_CUSTOM_CRON_ENABLED);

        if (!$customSchedule) {
            $orders->setPageSize(50);
        }

        $lastOrderId = $this
            ->getEventHelper()
            ->upsertOrders($orders)
        ;

        $this->saveConfig(
            CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_ORDER_ID,
            $lastOrderId
        );
    }

    /**
     * Inserts existing shipments into Communicator.
     *
     * @return void
     */
    private function insertExistingShipments()
    {
        $enabled = $this->isExistingSynchronisationEnabled();

        if (!$enabled) {
            return;
        }

        $lastShipmentId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SHIPMENT_ID);
        $stopShipmentId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_SHIPMENT_ID);

        if ($lastShipmentId === $stopShipmentId) {
            return;
        }

        $shipments = Mage::getModel('sales/order_shipment')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', array('gt'   => $lastShipmentId))
            ->addFieldToFilter('entity_id', array('lteq' => $stopShipmentId))
        ;

        $customSchedule = Mage::getStoreConfigFlag(CommunicatorCorp_Communicator_Model_Observer_Config::XML_PATH_COMMUNICATOR_GENERAL_CUSTOM_CRON_ENABLED);

        if (!$customSchedule) {
            $shipments->setPageSize(50);
        }

        $lastShipmentId = $this
            ->getEventHelper()
            ->upsertShipments($shipments)
        ;

        $this->saveConfig(
            CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SHIPMENT_ID,
            $lastShipmentId
        );
    }

    /**
     * Returns a boolean indicative of whether the synchronisation of existing data is enabled.
     *
     * @return bool
     */
    private function isExistingSynchronisationEnabled()
    {
        return Mage::getStoreConfigFlag(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_ENABLE_CRON_INSERT_EXISTING_DATA) &&
            Mage::getStoreConfigFlag(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_ENABLED);
    }

    /**
     * Saves a configuration value to the database and retains it in memory for the request.
     *
     * @param string $path  The configuration path.
     * @param mixed  $value The configuration value.
     *
     * @return void
     */
    private function saveConfig($path, $value)
    {
        $config = new Mage_Core_Model_Config();
        $config->saveConfig($path, $value);

        Mage::app()->getStore()->setConfig($path, $value);
    }
}

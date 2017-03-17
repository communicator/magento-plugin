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
 * Config observer.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Observer_Config extends CommunicatorCorp_Communicator_Model_Observer_Abstract
{
    const XML_PATH_COMMUNICATOR_GENERAL_CUSTOM_CRON_ENABLED = 'communicator/general/custom_cron';
    const XML_PATH_COMMUNICATOR_GENERAL_CUSTOM_CRON_DAY = 'communicator/general/custom_day';
    const XML_PATH_COMMUNICATOR_GENERAL_CUSTOM_CRON_TIME = 'communicator/general/custom_time';
    const XML_PATH_COMMUNICATOR_GENERAL_CUSTOM_CRON_FREQUENCY = 'communicator/general/custom_frequency';

    const COMMUNICATOR_CRON_EXPR_EXISTING = 'crontab/jobs/communicator_insert_existing/schedule/cron_expr';

    /**
     * {@inheritDoc}
     *
     * @param Varien_Event_Observer $observer The event observer.
     *
     * @return $this
     */
    public function observe(Varien_Event_Observer $observer)
    {
        $cacheId = 'communicator_enabled';
        $cache = Mage::app()->getCache();
        $enabledInCache = $cache->load($cacheId);
        $enabled = (int) $this->getDataHelper()->isEnabled();

        if (!$enabledInCache && $enabled) {
            $this->forwardStopIds();
        }

        $this
            ->updateSubscriberSyncConfig()
            ->updateProductSyncConfig()
            ->updateCustomerSyncConfig()
            ->updateOrderSyncConfig()
            ->updateShipmentSyncConfig()
            ->updateCronConfig()
            ->flushConfig()
        ;

        $cache->save((string) $enabled, $cacheId);

        return $this;
    }

    /**
     * Updates the configuration with last and stop customer IDs.
     *
     * @return $this
     */
    private function updateSubscriberSyncConfig()
    {
        $lastSubscriberId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SUBSCRIBER_ID);

        if ($lastSubscriberId) {
            return $this;
        }

        $stopSubscriberId = $this
            ->getDataHelper()
            ->getStopSubscriberId()
        ;

        $config = new Mage_Core_Model_Config();
        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SUBSCRIBER_ID, $lastSubscriberId);
        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_SUBSCRIBER_ID, $stopSubscriberId);

        return $this;
    }

    /**
     * Updates the configuration with last and stop customer IDs.
     *
     * @return $this
     */
    private function updateCustomerSyncConfig()
    {
        $lastCustomerId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_CUSTOMER_ID);

        if ($lastCustomerId) {
            return $this;
        }

        $stopCustomerId = $this
            ->getDataHelper()
            ->getStopCustomerId()
        ;

        $config = new Mage_Core_Model_Config();
        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_CUSTOMER_ID, $lastCustomerId);
        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_CUSTOMER_ID, $stopCustomerId);

        return $this;
    }

    /**
     * Updates the configuration with last and stop product IDs.
     *
     * @return $this
     */
    private function updateProductSyncConfig()
    {
        $lastProductId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_PRODUCT_ID);

        if ($lastProductId) {
            return $this;
        }

        $stopProductId = $this
            ->getDataHelper()
            ->getStopProductId()
        ;

        $config = new Mage_Core_Model_Config();
        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_PRODUCT_ID, $lastProductId);
        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_PRODUCT_ID, $stopProductId);

        return $this;
    }

    /**
     * Updates the configuration with last and stop order IDs.
     *
     * @return $this
     */
    private function updateOrderSyncConfig()
    {
        $lastOrderId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_ORDER_ID);

        if ($lastOrderId) {
            return $this;
        }

        $stopOrderId = $this
            ->getDataHelper()
            ->getStopOrderId()
        ;

        $config = new Mage_Core_Model_Config();
        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_ORDER_ID, $lastOrderId);
        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_ORDER_ID, $stopOrderId);

        return $this;
    }

    /**
     * Updates the configuration with last and stop shipment IDs.
     *
     * @return $this
     */
    private function updateShipmentSyncConfig()
    {
        $lastShipmentId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SHIPMENT_ID);

        if ($lastShipmentId) {
            return $this;
        }

        $stopShipmentId = $this
            ->getDataHelper()
            ->getStopOrderId()
        ;

        $config = new Mage_Core_Model_Config();
        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SHIPMENT_ID, $lastShipmentId);
        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_SHIPMENT_ID, $stopShipmentId);

        return $this;
    }

    /**
     * Updates the cron expressions for customer and product synchronisation.
     *
     * @return $this
     */
    private function updateCronConfig()
    {
        $enabled = Mage::getStoreConfigFlag(self::XML_PATH_COMMUNICATOR_GENERAL_CUSTOM_CRON_ENABLED);

        if ($enabled) {
            list($hour, $minute) = explode(',', Mage::getStoreConfig(self::XML_PATH_COMMUNICATOR_GENERAL_CUSTOM_CRON_TIME));

            $frequency = Mage::getStoreConfig(self::XML_PATH_COMMUNICATOR_GENERAL_CUSTOM_CRON_FREQUENCY);

            // If the frequency is weekly, use the configured day.
            $day = 'W' === $frequency ? (int) Mage::getStoreConfig(self::XML_PATH_COMMUNICATOR_GENERAL_CUSTOM_CRON_DAY) : '*';

            $expressionParts = array(
                (int) $minute, // Minute (0 - 59)
                (int) $hour,   // Hour (0 - 23)
                '*',           // Day of the month (1 - 31)
                '*',           // Month (1 - 12)
                $day,          // Day of the week (0 - 6)
            );

            $expression = join(' ', $expressionParts);
        } else {
            $expression = '*/5 * * * *';
        }

        try {
            Mage::getModel('core/config_data')
                ->load(self::COMMUNICATOR_CRON_EXPR_EXISTING, 'path')
                ->setValue($expression)
                ->setPath(self::COMMUNICATOR_CRON_EXPR_EXISTING)
                ->save()
            ;
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
        }

        return $this;
    }

    /**
     * Forward the stop IDs for synchronisation.
     *
     * @return $this
     */
    private function forwardStopIds()
    {
        $helper = $this->getDataHelper();

        $stopSubscriberId = $helper->getStopSubscriberId();
        $stopProductId = $helper->getStopProductId();
        $stopCustomerId = $helper->getStopCustomerId();
        $stopOrderId = $helper->getStopOrderId();

        $config = new Mage_Core_Model_Config();

        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_SUBSCRIBER_ID, $stopSubscriberId);
        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_PRODUCT_ID, $stopProductId);
        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_CUSTOMER_ID, $stopCustomerId);
        $config->saveConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_ORDER_ID, $stopOrderId);

        return $this;
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

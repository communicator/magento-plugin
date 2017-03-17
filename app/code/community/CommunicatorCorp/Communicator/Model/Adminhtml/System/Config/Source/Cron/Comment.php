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
 * Comment model for the cron field.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Adminhtml_System_Config_Source_Cron_Comment extends Mage_Core_Model_Config_Data
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /**
     * {@inheritDoc}
     *
     * @param Mage_Core_Model_Config_Element $element      The configuration element.
     * @param string                         $currentValue The value of the configuration element.
     *
     * @return string
     */
    public function getCommentText(Mage_Core_Model_Config_Element $element, $currentValue)
    {
        if ($currentValue) {
            $method = (string) $element->comment->method;
            $comment = $this->$method();
        } else {
            $comment = $this->getDataHelper()->__(<<< CRON_COMMENT
            If enabled, each time the cron is executed it will push a batch of existing data
            to Communicator. This process can take some time based on the amount of data
            requiring synchronisation, the progress of this process will be shown here when enabled.
CRON_COMMENT
            );
        }

        return $comment;
    }

    /**
     * Returns a string describing the progress made with existing order synchronisation.
     *
     * @return string
     */
    private function getProgressString()
    {
        $lastSubscriberId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SUBSCRIBER_ID);
        $stopSubscriberId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_SUBSCRIBER_ID);

        $lastProductId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_PRODUCT_ID);
        $stopProductId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_PRODUCT_ID);

        $lastCustomerId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_CUSTOMER_ID);
        $stopCustomerId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_CUSTOMER_ID);

        $lastOrderId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_ORDER_ID);
        $stopOrderId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_ORDER_ID);

        $lastShipmentId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SHIPMENT_ID);
        $stopShipmentId = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_SHIPMENT_ID);

        $unsynchronisedSubscriberCount = (int) Mage::getModel('newsletter/subscriber')
            ->getCollection()
            ->addFieldToFilter('subscriber_id', array('gt'   => $lastSubscriberId))
            ->addFieldToFilter('subscriber_id', array('lteq' => $stopSubscriberId))
            ->getSize()
        ;

        $unsynchronisedProductCount = (int) Mage::getModel('catalog/product')
            ->getCollection()
            ->addFieldToFilter('entity_id', array('gt'   => $lastProductId))
            ->addFieldToFilter('entity_id', array('lteq' => $stopProductId))
            ->getSize()
        ;

        $unsynchronisedCustomerCount = (int) Mage::getModel('customer/customer')
            ->getCollection()
            ->addFieldToFilter('entity_id', array('gt'   => $lastCustomerId))
            ->addFieldToFilter('entity_id', array('lteq' => $stopCustomerId))
            ->getSize()
        ;

        $unsynchronisedOrderCount = (int) Mage::getModel('sales/order')
            ->getCollection()
            ->addFieldToFilter('entity_id', array('gt'   => $lastOrderId))
            ->addFieldToFilter('entity_id', array('lteq' => $stopOrderId))
            ->getSize()
        ;

        $unsynchronisedShipmentCount = (int) Mage::getModel('sales/order_shipment')
            ->getCollection()
            ->addFieldToFilter('entity_id', array('gt'   => $lastShipmentId))
            ->addFieldToFilter('entity_id', array('lteq' => $stopShipmentId))
            ->getSize()
        ;

        return $this->getDataHelper()->__(
            '<strong>%d</strong> subscriber%s, <strong>%d</strong> product%s, <strong>%d</strong> customer%s, <strong>%d</strong> order%s and <strong>%d</strong> shipment%s remaining.',
            $unsynchronisedSubscriberCount,
            1 === $unsynchronisedSubscriberCount ? '' : 's',
            $unsynchronisedProductCount,
            1 === $unsynchronisedCustomerCount ? '' : 's',
            $unsynchronisedCustomerCount,
            1 === $unsynchronisedCustomerCount ? '' : 's',
            $unsynchronisedOrderCount,
            1 === $unsynchronisedOrderCount ? '' : 's',
            $unsynchronisedShipmentCount,
            1 === $unsynchronisedShipmentCount ? '' : 's'
        );
    }

    /**
     * Returns a string describing the progress made with abandoned basket dispatches.
     *
     * @return string
     */
    private function getAbandonedBasketProgressString()
    {
        $configuredInterval = (int) Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_ABANDONED_BASKET_INTERVAL);

        $startUpdatedAt = date(Varien_Date::DATETIME_PHP_FORMAT, time() - (($configuredInterval + 5) * 60));
        $endUpdatedAt   = date(Varien_Date::DATETIME_PHP_FORMAT, time() - ($configuredInterval * 60));

        $quoteCount = (int) Mage::getModel('sales/quote')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_active', array('eq' => 1))
            ->addFieldToFilter('customer_id', array('notnull' => true))
            ->addFieldToFilter('converted_at', array('null' => true))
            ->addFieldToFilter('updated_at', array('gteq' => $startUpdatedAt))
            ->addFieldToFilter('updated_at', array('lteq' => $endUpdatedAt))
            ->getSize()
        ;

        return $this->getDataHelper()->__(
            '%d enqueued quote%s.',
            $quoteCount,
            1 === $quoteCount ? '' : 's'
        );
    }
}

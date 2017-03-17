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
 * Synchronisation controller.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Adminhtml_CommunicatorSynchronisationController extends Mage_Adminhtml_Controller_Action
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /**
     * Action for resetting the synchronisation counters.
     *
     * @return void
     */
    public function resetSynchronisationAction()
    {
        $helper = $this->getDataHelper();

        $stopSubscriberId = $helper->getStopSubscriberId();
        $stopCustomerId = $helper->getStopCustomerId();
        $stopProductId = $helper->getStopProductId();
        $stopOrderId = $helper->getStopOrderId();
        $stopShipmentId = $helper->getStopShipmentId();

        $config = new Mage_Core_Model_Config();

        $config->saveConfig($helper::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SUBSCRIBER_ID, 0);
        $config->saveConfig($helper::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_SUBSCRIBER_ID, $stopSubscriberId);

        $config->saveConfig($helper::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_CUSTOMER_ID, 0);
        $config->saveConfig($helper::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_CUSTOMER_ID, $stopCustomerId);

        $config->saveConfig($helper::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_PRODUCT_ID, 0);
        $config->saveConfig($helper::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_PRODUCT_ID, $stopProductId);

        $config->saveConfig($helper::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_ORDER_ID, 0);
        $config->saveConfig($helper::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_ORDER_ID, $stopOrderId);

        $config->saveConfig($helper::XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SHIPMENT_ID, 0);
        $config->saveConfig($helper::XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_SHIPMENT_ID, $stopShipmentId);

        Mage::app()->getCacheInstance()->cleanType('config');
    }
}

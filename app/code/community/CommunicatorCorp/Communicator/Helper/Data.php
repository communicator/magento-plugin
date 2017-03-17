<?php

/*
 * CommunicatorCorp\Communicator
 *
 * Copyright © 2016 Rippleffect Studio Ltd
 * Rights reserved.
 *
 * PHP version 5.4+
 */

use CommunicatorCorp\Client\ObjectDefinition\ColumnMapping;
use CommunicatorCorp\Client\ObjectDefinition\CommunicatorCredentials;
use CommunicatorCorp\Client\Service\DataService;
use CommunicatorCorp\Client\Service\MessageService;
use CommunicatorCorp\Client\Service\ResponseService;

/**
 * Data helper.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Helper_Data extends CommunicatorCorp_Communicator_Helper_Abstract
{
    /** @const string The configuration path to the general enabled state. */
    const XML_PATH_COMMUNICATOR_GENERAL_ENABLED = 'communicator/general/enabled';

    /** @const string The configuration path to the username. */
    const XML_PATH_COMMUNICATOR_GENERAL_USERNAME = 'communicator/general/username';

    /** @const string The configuration path to the password. */
    const XML_PATH_COMMUNICATOR_GENERAL_PASSWORD = 'communicator/general/password';

    /** @const string The configuration path to the insert existing data enabled state. */
    const XML_PATH_COMMUNICATOR_GENERAL_ENABLE_CRON_INSERT_EXISTING_DATA = 'communicator/general/enable_cron_insert_existing_data';

    /** @const string The configuration path to the stop synchronised subscriber ID. */
    const XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_SUBSCRIBER_ID = 'communicator/general/stop_synchronised_subscriber_id';

    /** @const string The configuration path to the last synchronised subscriber ID. */
    const XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SUBSCRIBER_ID = 'communicator/general/last_synchronised_subscriber_id';

    /** @const string The configuration path to the stop synchronised customer ID. */
    const XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_CUSTOMER_ID = 'communicator/general/stop_synchronised_customer_id';

    /** @const string The configuration path to the last synchronised customer ID. */
    const XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_CUSTOMER_ID = 'communicator/general/last_synchronised_customer_id';

    /** @const string The configuration path to the stop synchronised product ID. */
    const XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_PRODUCT_ID = 'communicator/general/stop_synchronised_product_id';

    /** @const string The configuration path to the last synchronised product ID. */
    const XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_PRODUCT_ID = 'communicator/general/last_synchronised_product_id';

    /** @const string The configuration path to the stop synchronised order ID. */
    const XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_ORDER_ID = 'communicator/general/stop_synchronised_order_id';

    /** @const string The configuration path to the last synchronised order ID. */
    const XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_ORDER_ID = 'communicator/general/last_synchronised_order_id';

    /** @const string The configuration path to the stop synchronised shipment ID. */
    const XML_PATH_COMMUNICATOR_GENERAL_STOP_SYNCHRONISED_SHIPMENT_ID = 'communicator/general/stop_synchronised_shipment_id';

    /** @const string The configuration path to the last synchronised shipment ID. */
    const XML_PATH_COMMUNICATOR_GENERAL_LAST_SYNCHRONISED_SHIPMENT_ID = 'communicator/general/last_synchronised_shipment_id';

    /** @const string The configuration path to the contact table. */
    const XML_PATH_COMMUNICATOR_TABLE_CONTACT = 'communicator/table/contact';

    /** @const string The configuration path to the product table. */
    const XML_PATH_COMMUNICATOR_TABLE_PRODUCT = 'communicator/table/product';

    /** @const string The configuration path to the order table. */
    const XML_PATH_COMMUNICATOR_TABLE_ORDER = 'communicator/table/order';

    /** @const string The configuration path to the order item table. */
    const XML_PATH_COMMUNICATOR_TABLE_ORDER_ITEM = 'communicator/table/order_item';

    /** @const string The configuration path to the shipping table. */
    const XML_PATH_COMMUNICATOR_TABLE_SHIPPING = 'communicator/table/shipping';

    /** @const string The configuration path to the newsletter list. */
    const XML_PATH_COMMUNICATOR_LIST_NEWSLETTER = 'communicator/list/newsletter';

    /** @const string The configuration path to the transactional list. */
    const XML_PATH_COMMUNICATOR_LIST_TRANSACTIONAL = 'communicator/list/transactional';

    /** @const string The configuration path to the abandoned basket list. */
    const XML_PATH_COMMUNICATOR_LIST_ABANDONED_BASKET = 'communicator/list/abandoned_basket';

    /**
     * The Communicator credentials.
     *
     * @var CommunicatorCredentials
     */
    private $communicatorCredentials;

    /**
     * The data service.
     *
     * @var DataService
     */
    private $dataService;

    /**
     * The message service.
     *
     * @var MessageService
     */
    private $messageService;

    /**
     * The response service.
     *
     * @var ResponseService
     */
    private $responseService;

    /**
     * Instantiates a new instance of the Data helper.
     */
    final public function __construct()
    {
        $username = Mage::getStoreConfig(static::XML_PATH_COMMUNICATOR_GENERAL_USERNAME);
        $password = Mage::getStoreConfig(static::XML_PATH_COMMUNICATOR_GENERAL_PASSWORD);

        $this->communicatorCredentials = new CommunicatorCredentials(
            $username,
            Mage::helper('core')->decrypt($password)
        );
    }

    /**
     * Returns a boolean indicative of whether the extension is enabled.
     *
     * @param int|string|Mage_Core_Model_Store $store The store.
     *
     * @return bool
     *
     * @api
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_COMMUNICATOR_GENERAL_ENABLED, $store);
    }

    /**
     * Returns the Communicator data service.
     *
     * @return DataService
     *
     * @api
     */
    public function getDataService()
    {
        if (!$this->dataService instanceof DataService) {
            $this->dataService = new DataService($this->communicatorCredentials);
        }

        return $this->dataService;
    }


    /**
     * Returns the Communicator message service.
     *
     * @return MessageService
     *
     * @api
     */
    public function getMessageService()
    {
        if (!$this->messageService instanceof MessageService) {
            $this->messageService = new MessageService($this->communicatorCredentials);
        }

        return $this->messageService;
    }

    /**
     * Returns the Communicator response service.
     *
     * @return ResponseService
     *
     * @api
     */
    public function getResponseService()
    {
        if (!$this->responseService instanceof ResponseService) {
            $this->responseService = new ResponseService($this->communicatorCredentials);
        }

        return $this->responseService;
    }

    /**
     * Returns the ID of the latest subscriber when the method is called.
     *
     * @return int
     *
     * @api
     */
    public function getStopSubscriberId()
    {
        return (int) Mage::getModel('newsletter/subscriber')
            ->getCollection()
            ->addFieldToSelect('subscriber_id')
            ->setOrder('subscriber_id', 'desc')
            ->setPageSize(1)
            ->getFirstItem()
            ->getId()
        ;
    }

    /**
     * Returns the ID of the latest customer when the method is called.
     *
     * @return int
     *
     * @api
     */
    public function getStopCustomerId()
    {
        return (int) Mage::getModel('customer/customer')
            ->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSort('entity_id', 'desc')
            ->setPageSize(1)
            ->getFirstItem()
            ->getId()
        ;
    }

    /**
     * Returns the ID of the latest product when the method is called.
     *
     * @return int
     *
     * @api
     */
    public function getStopProductId()
    {
        return (int) Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSort('entity_id', 'desc')
            ->setPageSize(1)
            ->getFirstItem()
            ->getId()
        ;
    }

    /**
     * Returns the ID of the latest order when the method is called.
     *
     * @return int
     *
     * @api
     */
    public function getStopOrderId()
    {
        return (int) Mage::getModel('sales/order')
            ->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSort('entity_id', 'desc')
            ->setPageSize(1)
            ->getFirstItem()
            ->getId()
        ;
    }

    /**
     * Returns the ID of the latest shipment when the method is called.
     *
     * @return int
     *
     * @api
     */
    public function getStopShipmentId()
    {
        return (int) Mage::getModel('sales/order_shipment')
            ->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSort('entity_id', 'desc')
            ->setPageSize(1)
            ->getFirstItem()
            ->getId()
        ;
    }
}

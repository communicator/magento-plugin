<?php

/*
 * CommunicatorCorp\Communicator
 *
 * Copyright © 2016 Rippleffect Studio Ltd
 * Rights reserved.
 *
 * PHP version 5.4+
 */

use CommunicatorCorp\Client\ObjectDefinition\ClientTableColumn;

/**
 * Source for column options.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_System_Config_Source_Column extends CommunicatorCorp_Communicator_Model_System_Config_Source_Abstract
{
    /**
     * The source.
     *
     * @var string
     */
    private $source;

    /**
     * Returns the columns for the contact mapping.
     *
     * @return array
     */
    public function getContactColumns()
    {
        $this->source = CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_CONTACT;

        return parent::toOptionArray();
    }

    /**
     * Returns the columns for the product mapping.
     *
     * @return array
     */
    public function getProductColumns()
    {
        $this->source = CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_PRODUCT;

        return parent::toOptionArray();
    }

    /**
     * Returns the columns for the order mapping.
     *
     * @return array
     */
    public function getOrderColumns()
    {
        $this->source = CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_ORDER;

        return parent::toOptionArray();
    }

    /**
     * Returns the columns for the order item mapping.
     *
     * @return array
     */
    public function getOrderItemColumns()
    {
        $this->source = CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_ORDER_ITEM;

        return parent::toOptionArray();
    }

    /**
     * Returns the columns for the shipping mapping.
     *
     * @return array
     */
    public function getShippingColumns()
    {
        $this->source = CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_TABLE_SHIPPING;

        return parent::toOptionArray();
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $source The data source.
     *
     * @return array
     */
    protected function getData()
    {
        $clientTableId = (int) Mage::getStoreConfig($this->source);

        if (empty($clientTableId)) {
            return array();
        }

        $mappingOption = (new ClientTableColumn)
            ->setId(0)
            ->setName($this->getDataHelper()->__('No Mapping'))
        ;

        $columns = $this
            ->getDataHelper()
            ->getDataService()
            ->getClientTableColumns($clientTableId)
        ;

        array_unshift($columns, $mappingOption);

        return $columns;
    }
}

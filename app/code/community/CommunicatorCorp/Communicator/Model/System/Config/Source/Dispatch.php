<?php

/*
 * CommunicatorCorp\Communicator
 *
 * Copyright © 2016 Rippleffect Studio Ltd
 * Rights reserved.
 *
 * PHP version 5.4+
 */

use CommunicatorCorp\Client\ObjectDefinition\MailingList;

/**
 * Source for dispatch options.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_System_Config_Source_Dispatch extends CommunicatorCorp_Communicator_Model_System_Config_Source_Abstract
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /**
     * The source for the dispatches.
     *
     * @var string
     */
    private $source;

    /**
     * Returns the dispatches associated to the newsletter mailing list.
     *
     * @return array
     */
    public function getCustomerDispatches()
    {
        $this->source = CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_LIST_NEWSLETTER;

        return $this->getMagentoDispatch() + parent::toOptionArray();
    }

    /**
     * Returns the dispatches associated to the transactional mailing list.
     *
     * @return array
     */
    public function getTransactionalDispatches()
    {
        $this->source = CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_LIST_TRANSACTIONAL;

        return $this->getMagentoDispatch() + parent::toOptionArray();
    }

    /**
     * Returns the dispatches associated to the abandoned basket mailing list.
     *
     * @return array
     */
    public function getAbandonedBasketDispatches()
    {
        $this->source = CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_LIST_ABANDONED_BASKET;

        return parent::toOptionArray();
    }

    /**
     * Returns all dispatches associated to all mailing lists within Communicator.
     *
     * @return array
     */
    public function getDispatches()
    {
        $dispatches = array();

        try {
            $mailingLists = $this
                ->getDataHelper()
                ->getDataService()
                ->getMailingLists()
            ;

            /** @var MailingList $mailingList */
            foreach ($mailingLists as $mailingList) {
                array_push(
                    $dispatches,
                    $this
                        ->getDataHelper()
                        ->getMessageService()
                        ->getTriggerDispatches($mailingList->getId())
                );
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
        }

        return $dispatches;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getData()
    {
        $mailingListId = (int) Mage::getStoreConfig($this->source);

        if (empty($mailingListId)) {
            return array();
        }

        return $this
            ->getDataHelper()
            ->getMessageService()
            ->getTriggerDispatches($mailingListId)
        ;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function getLabelProperty()
    {
        return 'SubjectLine';
    }

    /**
     * Returns an option for the native Magento dispatch.
     *
     * @return array
     */
    private function getMagentoDispatch()
    {
        $helper = $this->getDataHelper();

        return array($helper->__('Use Magento'));
    }
}

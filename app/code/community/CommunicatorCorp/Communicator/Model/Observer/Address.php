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
 * Address observer.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Observer_Address extends CommunicatorCorp_Communicator_Model_Observer_Abstract
{
    /**
     * Observes an address change and upserts the customer to Communicator.
     *
     * @param Varien_Event_Observer $observer The event observer.
     *
     * @return $this
     */
    public function observe(Varien_Event_Observer $observer)
    {
        if (!$this->getDataHelper()->isEnabled()) {
            return $this;
        }

        $customerAddress = $observer->getCustomerAddress();
        $customer = $customerAddress->getCustomer();

        $this
            ->getEventHelper()
            ->upsertCustomer($customer)
        ;

        return $this;
    }
}

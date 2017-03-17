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
 * Customer observer.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Observer_Customer extends CommunicatorCorp_Communicator_Model_Observer_Abstract
{
    /**
     * {@inheritDoc}
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

        $eventName = $observer->getEvent()->getName();

        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $observer->getCustomer();

        switch ($eventName) {
            case 'customer_save_after':
                $this->onCustomerSaveAfter($customer);

                break;
            case 'customer_login':
                $this->onCustomerLogin($customer);
                break;
        }

        return $this;
    }

    /**
     * Called when the customer_save_after event is fired.
     *
     * @param Mage_Customer_Model_Customer $customer The customer.
     *
     * @return void
     */
    private function onCustomerSaveAfter(Mage_Customer_Model_Customer $customer)
    {
        $this
            ->getEventHelper()
            ->upsertCustomer($customer)
        ;
    }

    /**
     * Called when the customer_login event is fired.
     *
     * @param Mage_Customer_Model_Customer $customer The customer.
     *
     * @return void
     */
    private function onCustomerLogin(Mage_Customer_Model_Customer $customer)
    {
        $this
            ->getEventHelper()
            ->updateCustomerSubscription($customer)
        ;
    }
}

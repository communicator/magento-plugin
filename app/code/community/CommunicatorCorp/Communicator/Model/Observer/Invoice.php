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
 * Invoice observer.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Observer_Invoice extends CommunicatorCorp_Communicator_Model_Observer_Abstract
{
    /**
     * Observes the new invoice event.
     *
     * @param Varien_Event_Observer $observer The event observer.
     *
     * @return $this
     */
    public function observe(Varien_Event_Observer $observer)
    {
        $enabled = $this->getDataHelper()->isEnabled() &&
            Mage::getStoreConfigFlag(CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_INVOICE_ENABLED);

        if (!$enabled) {
            return $this;
        }

        $triggeredDispatchId = Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_INVOICE_DISPATCH);

        if (!$triggeredDispatchId) {
            return $this;
        }

        if ($observer->getEvent()->getName() === 'sales_model_order_invoice_send_email_before') {
            $observer->getData('data')->setPreventDefault(true);
        }

        $this
            ->getEventHelper()
            ->upsertOrder($observer->getInvoice()->getOrder(), $triggeredDispatchId)
        ;

        return $this;
    }
}

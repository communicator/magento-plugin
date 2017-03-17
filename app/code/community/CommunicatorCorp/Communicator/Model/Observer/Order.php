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
 * Order observer.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Lukasz Lewandowski <llewandowski@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Observer_Order extends CommunicatorCorp_Communicator_Model_Observer_Abstract
{
    /**
     * Observes the new order event.
     *
     * @param Varien_Event_Observer $observer The event observer.
     *
     * @return $this
     */
    public function observe(Varien_Event_Observer $observer)
    {
        $enabled = $this->getDataHelper()->isEnabled() &&
            Mage::getStoreConfigFlag(CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_ORDER_ENABLED);

        if (!$enabled) {
            return $this;
        }

        $triggeredDispatchId = Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_ORDER_DISPATCH);

        if (!$triggeredDispatchId) {
            return $this;
        }

        if ($observer->getEvent()->getName() === 'sales_model_order_send_email_before') {
            $observer->getData('data')->setPreventDefault(true);
        }

        $this
            ->getEventHelper()
            ->upsertOrder($observer->getOrder(), $triggeredDispatchId)
        ;

        return $this;
    }
}

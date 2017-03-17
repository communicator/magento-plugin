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
 * Shipment observer.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@honestempire.com>
 */
class CommunicatorCorp_Communicator_Model_Observer_Shipment extends CommunicatorCorp_Communicator_Model_Observer_Abstract
{
    /**
     * Observes the new shipment event.
     *
     * @param Varien_Event_Observer $observer The event observer.
     *
     * @return $this
     */
    public function observe(Varien_Event_Observer $observer)
    {
        $enabled = $this->getDataHelper()->isEnabled() &&
            Mage::getStoreConfigFlag(CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_SHIPMENT_ENABLED);

        if (!$enabled) {
            return $this;
        }

        $triggeredDispatchId = Mage::getStoreConfig(CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_SHIPMENT_DISPATCH);

        if (!$triggeredDispatchId) {
            return $this;
        }
        
        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = $observer->getShipment();
        
        $this
            ->getEventHelper()
            ->upsertShipment($shipment)
            ->upsertOrder($shipment->getOrder(), $triggeredDispatchId, $shipment)
        ;

        return $this;
    }
}

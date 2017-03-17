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
 * Rewrite of the sales order model class
 *
 * Add send order email dispatch events.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Lukasz Lewandowski <llewandowski@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Sales_Order extends Mage_Sales_Model_Order
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /**
     * Send email with order data
     *
     * @return Mage_Sales_Model_Order
     */
    public function sendNewOrderEmail()
    {
        $eventData = new Varien_Object([
            'prevent_default' => false
        ]);
        Mage::dispatchEvent('sales_model_order_send_email_before', [
            'order' => $this,
            'data'  => $eventData
        ]);

        if ($eventData->getPreventDefault()) {
            return $this;
        }

        parent::sendNewOrderEmail();
        Mage::dispatchEvent('sales_model_order_send_email_after', ['order' => $this]);

        return $this;
    }
}

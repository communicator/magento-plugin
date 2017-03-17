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
 * Rewrite of the sales invoice model class
 *
 * Add send invoice email dispatch events.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Lukasz Lewandowski <llewandowski@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Sales_Invoice extends Mage_Sales_Model_Order_Invoice
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /**
     * Send email with invoice data
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function sendEmail($notifyCustomer = true, $comment = '')
    {
        $eventData = new Varien_Object([
            'prevent_default' => false
        ]);
        Mage::dispatchEvent('sales_model_order_invoice_send_email_before', [
            'invoice' => $this,
            'data'    => $eventData
        ]);

        if ($eventData->getPreventDefault()) {
            return $this;
        }

        parent::sendEmail($notifyCustomer, $comment);
        Mage::dispatchEvent('sales_model_order_invoice_send_email_after', ['invoice' => $this]);

        return $this;
    }
}

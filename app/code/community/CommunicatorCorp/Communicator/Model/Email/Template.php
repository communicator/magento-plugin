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
 * Rewrite of the email template class.
 *
 * This rewrite is responsible for preventing Magento sending it's own emails
 * if an equivalent dispatch is configured within Communicator.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Email_Template extends Aschroder_SMTPPro_Model_Email_Template
{
    /**
     * {@inheritDoc}
     *
     * @param string       $templateId Template ID.
     * @param string|array $sender     Sender information.
     * @param string       $email      Recipient's email address.
     * @param string       $name       Recipient's name.
     * @param array        $vars       Variables which can be used within the template.
     * @param int|null     $storeId    Store ID.
     *
     * @return Mage_Core_Model_Email_Template|$this
     */
    public function sendTransactional($templateId, $sender, $email, $name, array $vars = array(), $storeId = null)
    {
        if ($this->canDispatch($templateId)) {
            return parent::sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
        } else {
            $this->setSentSuccess(true);
        }

        return $this;
    }

    /**
     * Returns a boolean indicative of whether Magento can dispatch it's own email.
     *
     * @param string $templateId Template ID.
     *
     * @return bool
     */
    private function canDispatch($templateId)
    {
        switch ($templateId) {
            case 'customer_create_account_email_template':
                $enabled  = CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_CUSTOMER_ENABLED;
                $dispatch = CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_CUSTOMER_DISPATCH;

                break;
            case 'newsletter_subscription_success_email_template':
            case 'newsletter_subscription_un_email_template':
                $enabled = CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_SUBSCRIBER_ENABLED;
                $dispatch = CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_SUBSCRIBER_DISPATCH;

                break;
            case 'sales_email_order_template':
            case 'sales_email_order_guest_template':
                $enabled = CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_ORDER_ENABLED;
                $dispatch = CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_ORDER_DISPATCH;

                break;
            case 'sales_email_invoice_template':
            case 'sales_email_invoice_guest_template':
                $enabled = CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_INVOICE_ENABLED;
                $dispatch = CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_INVOICE_DISPATCH;

                break;
            case 'sales_email_shipment_template':
            case 'sales_email_shipment_guest_template':
                $enabled = CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_SHIPMENT_ENABLED;
                $dispatch = CommunicatorCorp_Communicator_Helper_Event::XML_PATH_COMMUNICATOR_EVENT_SHIPMENT_DISPATCH;

                break;
            default:
                return true;
        }

        $dispatchConfigured = (bool) Mage::getStoreConfig($dispatch);
        $extensionEnabled   = Mage::getStoreConfigFlag(CommunicatorCorp_Communicator_Helper_Data::XML_PATH_COMMUNICATOR_GENERAL_ENABLED);
        $dispatchEnabled    = Mage::getStoreConfigFlag($enabled);

        return !($extensionEnabled && $dispatchEnabled && $dispatchConfigured);
    }
}

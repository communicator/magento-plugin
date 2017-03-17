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
 * Class for rendering the button required to reset the synchronisation counters.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Block_Adminhtml_System_Config_Form_ResetButton extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /**
     * Returns the URL to the synchronisation reset endpoint.
     *
     * @return string
     */
    public function getAjaxResetSynchronisationUrl()
    {
        return Mage::helper('adminhtml')
            ->getUrl('adminhtml/adminhtml_CommunicatorSynchronisation/resetSynchronisation')
        ;
    }

    /**
     * Returns the HTML for the reset synchronisation button.
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this
            ->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'      => 'communicator_general_reset_button',
                'label'   => $this->getDataHelper()->__('Reset Synchronisation'),
                'onclick' => 'javascript:resetSynchronisation(); return false;',
            ))
        ;

        return $button->toHtml();
    }

    /**
     * Magento class constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('communicator/system/config/form/reset_button.phtml');
    }

    /**
     * Returns the HTML of the current element.
     *
     * @param Varien_Data_Form_Element_Abstract $element Form element
     *
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }
}

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
 * Class for rendering the button used to test the connection to Communicator.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Block_Adminhtml_System_Config_Form_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /**
     * Returns the URL to the Communicator connection test endpoint.
     *
     * @return string
     */
    public function getAjaxTestCommunicatorConnectionUrl()
    {
        return Mage::helper('adminhtml')
            ->getUrl('adminhtml/adminhtml_CommunicatorConnection/testCommunicatorConnection')
        ;
    }

    /**
     * Returns the HTML for the test connection button.
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this
            ->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'      => 'communicator_general_test_button',
                'label'   => $this->getDataHelper()->__('Test Communicator Connection'),
                'onclick' => 'javascript:testCommunicatorConnection(); return false;',
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

        $this->setTemplate('communicator/system/config/form/button.phtml');
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

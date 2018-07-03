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
 * Abstract configuration source
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
abstract class CommunicatorCorp_Communicator_Model_System_Config_Source_Abstract
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /**
     * Returns the data required for the `toOptionArray` method.
     *
     * This method in conjunction with `getLabelProperty` and `getValueProperty` is
     * used to build an option array suitable for a Magento configuration select field.
     *
     * @return array
     */
    abstract protected function getData();

    /**
     * Returns the configuration source as an array of options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        if (false === $this->getDataHelper()->isEnabled()) {
            return $options;
        }

        try {
            $data = $this->getData();
            if ($data == NULL) {
                return $options;
            }

            // TODO: API sends back an object if there's only one result. We'll need to
            // handle this case otherwise we'll error out trying to loop over the object.
            $data = $this->getData();
            if (!is_array($data)) {
                $data = [$data];
            }

            foreach ($data as $option) {
                $value = $option->{sprintf('get%s', $this->getValueProperty())}();
                $label = $option->{sprintf('get%s', $this->getLabelProperty())}();

                $options[$value] = $label;
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
        }

        return $options;
    }

    /**
     * Returns the property that is to be used as the option string.
     *
     * @return string
     */
    protected function getLabelProperty()
    {
        return 'Name';
    }

    /**
     * Returns the property that is to be used as the option value.
     *
     * @return string
     */
    protected function getValueProperty()
    {
        return 'Id';
    }
}

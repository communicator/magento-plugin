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
 * Sales observer.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Observer_Sales extends CommunicatorCorp_Communicator_Model_Observer_Abstract
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

        $this
            ->getEventHelper()
            ->upsertOrder($observer->getOrder())
        ;

        return $this;
    }
}

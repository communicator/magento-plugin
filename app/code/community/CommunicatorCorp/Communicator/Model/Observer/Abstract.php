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
 * Abstract observer.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
abstract class CommunicatorCorp_Communicator_Model_Observer_Abstract
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /**
     * Observes an event.
     *
     * @param Varien_Event_Observer $observer The event observer.
     *
     * @return $this
     */
    abstract public function observe(Varien_Event_Observer $observer);
}

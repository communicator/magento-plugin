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
 * Connection controller.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Adminhtml_CommunicatorConnectionController extends Mage_Adminhtml_Controller_Action
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /**
     * Action for testing the connection to the Communicator API.
     *
     * @return void
     */
    public function testCommunicatorConnectionAction()
    {
        $canConnect = $this->canConnect();

        Mage::app()
            ->getResponse()
            ->setHeader('Content-type', 'application/json')
            ->setBody(json_encode(array('connected' => $canConnect)))
        ;
    }

    /**
     * Tests whether a connection to the Communicator API can be established.
     *
     * @return bool
     */
    private function canConnect()
    {
        try {
            $tables = $this
                ->getDataHelper()
                ->getDataService()
                ->getClientTables()
            ;
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
        }

        return isset($tables);
    }
}

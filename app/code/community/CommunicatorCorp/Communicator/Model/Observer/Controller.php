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
 * Controller observer.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Observer_Controller extends CommunicatorCorp_Communicator_Model_Observer_Abstract
{
    /**
     * Boolean indicative of whether the front controller has been initialised.
     *
     * @var bool
     */
    private static $frontInitialised = false;

    /**
     * {@inheritDoc}
     *
     * @param Varien_Event_Observer $observer The event observer.
     *
     * @return $this
     */
    public function observe(Varien_Event_Observer $observer)
    {
        if (self::$frontInitialised) {
            return $this;
        }

        // The first path to try is app/code/community/CommunicatorCorp/Communicator - this is used when installing
        // the module from an archive (i.e. the vendor folder is packaged into the archive file)
        $pathsToAttempt = array(
            sprintf('%s%s/vendor/autoload.php', __DIR__, str_repeat('/..', 2))
        );

        // The next paths are attempts to find the outer project root. 7 levels up is the Magento root, then we look
        // a further few levels higher in case the composer.json is outside of the Magento root
        for ($i = 0; $i < 4; $i++) {
            $pathsToAttempt[] = sprintf('%s%s/vendor/autoload.php', __DIR__, str_repeat('/..', 7 + $i));
        }

        foreach ($pathsToAttempt as $autoloaderPath) {
            $realPath = realpath($autoloaderPath);

            if (false !== $realPath) {
                require $realPath;
                self::$frontInitialised = true;
                break;
            }
        }

        if (false === self::$frontInitialised) {
            throw new RuntimeException('The autoloader could not be found.');
        }

        return $this;
    }
}

<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2025, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocatorBundle\EventListener\KernelResponse;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;


class BackendAssetsListener implements EventSubscriberInterface {


    /**
     * @var Contao\CoreBundle\Routing\ScopeMatcher
     */
    protected ScopeMatcher $scopeMatcher;


    public function __construct( ScopeMatcher $scopeMatcher ) {
        $this->scopeMatcher = $scopeMatcher;
    }


    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }


    /**
     * {@inheritdoc}
     */
    public function onKernelRequest( RequestEvent $e ): void {

        $request = $e->getRequest();

        if( $this->scopeMatcher->isBackendRequest($request) ) {
            $GLOBALS['TL_CSS'][] = 'bundles/storelocator/backend.css';
        }
    }
}

<?php

namespace RRaven\Bundle\LaughingbearBundle\EventListener;

use RRaven\Bundle\LaughingbearBundle\Controller\LaughingbearController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class AutoFlush
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure. This is not usual in Symfony2 but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller) || !($controller[0] instanceof LaughingbearController)) {
            return;
        }

        $event->getRequest()->attributes->set("em", $controller[0]->getEntityManager());
    }
    
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->getRequest()->attributes->get('needs_flush')) {
            return;
        }
        
        $em = $event->getRequest()->attributes->get("em");
        /* @var $em \Doctrine\ORM\EntityManager */
        $em->flush();
    }
}
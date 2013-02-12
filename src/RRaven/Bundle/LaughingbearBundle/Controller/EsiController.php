<?php

namespace RRaven\Bundle\LaughingbearBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use RRaven\Bundle\LaughingbearBundle\Entity;
use RRaven\Bundle\LaughingbearBundle\Annotations\Menu;

/**
 * @Route("/esi")
 */
class EsiController extends LaughingbearController
{

    /**
     * @Route("/hello/", name="esi_hello")
     * @Template()
     */
    public function helloAction()
    {
        return array('name' => $this->getUser()->getUsername());
    }
    
    /**
     * @Route("/menus/top/", name="esi_menus_top")
     * @Template
     */
    public function menusTopAction()
    {
        $response = new Response();
        $user = $this->getUser();
        $username = "NOBODY";
        if ($user) {
            $username = $user->getUsername();
        }
        $etag = md5($username);
        $response->setEtag($etag);
        $response->setSharedMaxAge(600);
        $response->setVary("Cookie");
        
        $this->get("logger")->debug("wark");
        
        if ($response->isNotModified($this->getRequest())) {
            return $response;
        } else {
            //return array();
            return $this->render("RRavenLaughingbearBundle:Esi:menusTop.html.twig", array(), $response);
        }
    }

}

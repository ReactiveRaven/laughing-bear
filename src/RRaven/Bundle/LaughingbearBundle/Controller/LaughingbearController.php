<?php

namespace RRaven\Bundle\LaughingbearBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\Security\Core\SecurityContext;

abstract class LaughingbearController extends Controller
{
	private $_securityContext = null;
	private $_browser = null;
	
  /**
   * @return Session
   */
  protected function getSession()
  {
    return $this->get("session");
  }
  
  /**
   * @return SecurityContext
   */
  protected function getSecurityContext()
  {
    return 
			(
				$this->_securityContext 
					? $this->_securityContext 
					: $this->_securityContext =  $this->get("security.context")
			)
		;
  }
	
	protected function isLoggedIn()
	{
		return $this->getSecurityContext()->isGranted('IS_AUTHENTICATED_FULLY');
	}
  
  protected function getAccessToken()
  {
    return $this->getSecurityContext()->getToken()->getAccessToken();
  }
  
  /**
   * @return \Buzz\Browser
   */
  protected function getBuzzBrowser()
  {
    return 
			(
				$this->_browser
					? $this->_browser
					: $this->_browser = $this->container->get('buzz')->getBrowser('default')
			)
		;
  }
}
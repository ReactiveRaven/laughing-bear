<?php

namespace RRaven\Bundle\LaughingbearBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\Security\Core\SecurityContext;
use RRaven\Bundle\LaughingbearBundle\Entity;

abstract class LaughingbearController extends Controller
{
	private $_securityContext = null;
	private $_browser = null;
    private $_api_map_helper;
    private $_accessToken = null;
    private $_em = null;
    private $_needs_flush = false;
    private $_github_user = null;
	
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
  
    protected function getAccessTokenString()
    {
      return $this->getSecurityContext()->getToken()->getAccessToken();
    }
  
    /**
	 * Returns an instance of ApiMapHelper for processing data into entities
	 * 
	 * @return \RRaven\Bundle\LaughingbearBundle\Helper\ApiMapHelper
	 */
	protected function getApiMapHelper() {
		return 
			(
				$this->_api_map_helper
					? $this->_api_map_helper
					: $this->_api_map_helper = $this->container->get("rraven.helper.apimap")
			)
		;
	}
    
    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return ($this->_em === null ? $this->_em = $this->getDoctrine()->getEntityManager() : $this->_em);
    }
    
    /**
     * Gets the access token for the currently active user.
     * 
     * @return Entity\GithubAccessToken
     * @throws \Exception if no access token (or user) found.
     */
    protected function getAccessTokenObject() {
        if ($this->_accessToken) {
            return $this->_accessToken;
        }
        $em = $this->getEntityManager();
        
        $githubUser_repo = $em->getRepository("RRavenLaughingbearBundle:GithubUser");
        $githubUser = $githubUser_repo->findOneBy(array("login" => $this->getUser()->getUsername()));
        /* @var $githubUser Entity\GithubUser */
        return $this->_accessToken = $githubUser->getAccessToken();
    }
    
    /**
     * Gets the currently active Github user
     * 
     * @return Entity\GithubUser
     */
    protected function getGithubUser() {
        if ($this->_github_user === null) {
            $em = $this->getEntityManager();
            $githubUser_repo = $em->getRepository("RRavenLaughingbearBundle:GithubUser");
            $this->_github_user = $githubUser_repo->findOneBy(array("login" => $this->getUser()->getUsername()));
        }
        return $this->_github_user;
    }
    
    /**
     * 
     * @param type $url
     * @param type $headers
     * @param \RRaven\Bundle\LaughingbearBundle\Entity\GithubAccessToken $accessToken
     * @return \RRaven\Bundle\LaughingbearBundle\Utility\Buzz\Message\Response\Response
     */
    protected function makeGetRequest($url, $headers = array(), Entity\GithubAccessToken $accessToken = null) {
        $this->markNeedsFlush();
        if ($accessToken === null) {
            $accessToken = $this->getAccessTokenObject();
        }
        /* @var $accessToken Entity\GithubAccessToken */
        return $accessToken->makeGetRequest($url, $headers);
    }
    
    /**
     * 
     * @param type $url
     * @param type $headers
     * @param \RRaven\Bundle\LaughingbearBundle\Entity\GithubAccessToken $accessToken
     * @return \RRaven\Bundle\LaughingbearBundle\Utility\Buzz\Message\Response\Response
     */
    protected function makePostRequest($url, $headers = array(), Entity\GithubAccessToken $accessToken = null) {
        $this->markNeedsFlush();
        if ($accessToken === null) {
            $accessToken = $this->getAccessTokenObject();
        }
        /* @var $accessToken Entity\GithubAccessToken */
        return $accessToken->makePostRequest($url, $headers);
    }
    
    /**
     * 
     * @param type $url
     * @param type $headers
     * @param \RRaven\Bundle\LaughingbearBundle\Entity\GithubAccessToken $accessToken
     * @return \RRaven\Bundle\LaughingbearBundle\Utility\Buzz\Message\Response\Response
     */
    protected function makePutRequest($url, $headers = array(), Entity\GithubAccessToken $accessToken = null) {
        $this->markNeedsFlush();
        if ($accessToken === null) {
            $accessToken = $this->getAccessTokenObject();
        }
        /* @var $accessToken Entity\GithubAccessToken */
        return $accessToken->makePutRequest($url, $headers);
    }
    
    /**
     * 
     * @param type $url
     * @param type $headers
     * @param \RRaven\Bundle\LaughingbearBundle\Entity\GithubAccessToken $accessToken
     * @return \RRaven\Bundle\LaughingbearBundle\Utility\Buzz\Message\Response\Response
     */
    protected function makeDeleteRequest($url, $headers = array(), Entity\GithubAccessToken $accessToken = null) {
        $this->markNeedsFlush();
        if ($accessToken === null) {
            $accessToken = $this->getAccessTokenObject();
        }
        /* @var $accessToken Entity\GithubAccessToken */
        return $accessToken->makeDeleteRequest($url, $headers);
    }
    
    /**
     * 
     * @param type $url
     * @param type $headers
     * @param \RRaven\Bundle\LaughingbearBundle\Entity\GithubAccessToken $accessToken
     * @return \RRaven\Bundle\LaughingbearBundle\Utility\Buzz\Message\Response\Response
     */
    protected function makePatchRequest($url, $headers = array(), Entity\GithubAccessToken $accessToken = null) {
        $this->markNeedsFlush();
        if ($accessToken === null) {
            $accessToken = $this->getAccessTokenObject();
        }
        /* @var $accessToken Entity\GithubAccessToken */
        return $accessToken->makePatchRequest($url, $headers);
    }
    
    /**
     * 
     * @param type $url
     * @param type $headers
     * @param \RRaven\Bundle\LaughingbearBundle\Entity\GithubAccessToken $accessToken
     * @return \RRaven\Bundle\LaughingbearBundle\Utility\Buzz\Message\Response\Response
     */
    protected function makeHeadRequest($url, $headers = array(), Entity\GithubAccessToken $accessToken = null) {
        $this->markNeedsFlush();
        if ($accessToken === null) {
            $accessToken = $this->getAccessTokenObject();
        }
        /* @var $accessToken Entity\GithubAccessToken */
        return $accessToken->makeHeadRequest($url, $headers);
    }
    
    protected function actionStart($action, $json = null) {
        if (!is_string($json)) {
            $json = json_encode($json);
        }
        
        $action = $this->ensureAction($action);
        if ($json) {
            $action->setJson($json);
        }
        $action->setComplete(false);
        $action->setActive(true);
    }
    
    protected function actionEnqueue($action, $json = null) {
        if (!is_string($json)) {
            $json = json_encode($json);
        }
        
        $action = $this->ensureAction($action, true);
        if ($json) {
            $action->setJson($json);
        }
        $action->setComplete(false);
        $action->setActive(false);
    }
    
    protected function actionPause($action, $json = null) {
        if (!is_string($json)) {
            $json = json_encode($json);
        }
        
        $action = $this->ensureAction($action);
        if ($json) {
            $action->setJson($json);
        }
        $action->setActive(false);
    }
    
    private function ensureAction($action_string, $canDuplicate = false) {
        $this->markNeedsFlush();
        
        $currentUser = $this->getGithubUser();
        
        $actionRepo = $this->getEntityManager()->getRepository("RRavenLaughingbearBundle:Action");
        $existingActions = $actionRepo->findBy(array("user" => $currentUser, "action" => $action_string), array("updated" => "DESC"));
        if (!$canDuplicate && count($existingActions)) {
            $action = $existingActions[0];
            /* @var $existingAction Entity\Action */
        } else {
            $action = new Entity\Action();
            $action->setAction($action_string);
            $action->setUser($currentUser);
            $this->getEntityManager()->persist($action);
        }
        
        return $action;
    }
    
    protected function actionResume($action, $json = null) {
        if (!is_string($json)) {
            $json = json_encode($json);
        }
        
        $action = $this->ensureAction($action);
        if ($json) {
            $action->setJson($json);
        }
        $action->setActive(true);
    }
    
    protected function actionGetLatest($action) {
        
        $actionRepo = $this->getEntityManager()->getRepository("RRavenLaughingbearBundle:Action");
        $existingActions = $actionRepo->findBy(
            array(
                "user" => $this->getGithubUser(), 
                "action" => $action
            ), 
            array(
                "updated" => "DESC"
            )
        );
        
        $result = false;
        if (count($existingActions)) {
            $result = $existingActions[0];
        }
        
        return $result;
    }
    
    protected function actionCompletedRecently($action, $seconds = 300) {
        
        $currentUser = $this->getGithubUser();
        
        $actionRepo = $this->getEntityManager()->getRepository("RRavenLaughingbearBundle:Action");
        $existingActions = $actionRepo->findBy(array("user" => $currentUser, "action" => $action, "complete" => true), array("updated" => "DESC"));
        
        $result = false;
        if (count($existingActions)) {
            foreach ($existingActions as $action /* @var $action Entity\Action */) {
                // If the updated timestamp is less than $seconds ago, make result true.
                $result = $result ? $result : time() - $seconds <= $action->getUpdated()->getTimestamp();
            }
        }
        
        return $result;
    }
    
    protected function actionComplete($action, $json = null) {
        if (!is_string($json)) {
            $json = json_encode($json);
        }
        
        $action = $this->ensureAction($action);
        if ($json) {
            $action->setJson($json);
        }
        $action->setComplete(true);
        $action->setActive(false);
    }
    
    protected function markNeedsFlush()
    {
        if (!$this->_needs_flush) {
            $this->getRequest()->attributes->set("needs_flush", true);
            $this->_needs_flush = true;
        }
    }
}
<?php

namespace RRaven\Bundle\HWIOAuthOverrideBundle\Controller;

use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException,
    HWI\Bundle\OAuthBundle\Controller\ConnectController as BaseConnectController,
    HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken,
    HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Core\SecurityContext,
    Symfony\Component\Form\Form,
    Symfony\Component\Security\Core\User\UserInterface;
use RRaven\Bundle\LaughingbearBundle\Entity\GithubAccessToken,
    RRaven\Bundle\LaughingbearBundle\Entity\GithubUser,
    RRaven\Bundle\LaughingbearBundle\Entity\GithubRateLimit,
    RRaven\Bundle\LaughingbearBundle\Utility\Github\Spider\Message;


/**
 * ConnectController overrides
 *
 * @author ReactiveRaven <reactiveraven@reactiveraven.co.uk>
 */
class ConnectController extends BaseConnectController {

  private $error = null;
	private $_api_map_helper;

  public function registrationAction(Request $request, $key) {
    
    $this->error = $request->getSession()->get('_hwi_oauth.registration_error.' . $key);
    
    $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
    $connect = $this->container->getParameter('hwi_oauth.connect');

    $session = $request->getSession();
    $error = $session->get('_hwi_oauth.registration_error.' . $key);
    $session->remove('_hwi_oauth.registration_error.' . $key);

    if (!$connect || $hasUser || !($error instanceof AccountNotLinkedException) || (time() - $key > 300)) {
      return parent::registrationAction($request, $key); // maybe they've fixed this upstream?
    }

    $userInformation = $this->getResourceOwnerByName($error->getResourceOwnerName())
      ->getUserInformation($error->getAccessToken());
    
    $userManager = $this->container->get('fos_user.user_manager');
    $user = $userManager->createUser(); 
    
    /* @var $userInformation UserResponseInterface */
    
		$em = $this->container->get("doctrine")->getEntityManager();
		/* @var $em \Doctrine\ORM\EntityManager */
		
		try
		{
			$github_user = $em->getRepository("RRavenLaughingbearBundle:GithubUser")->findOneByGithubId($userInformation->getUsername());
		} catch (\Exception $exception) {
			$exception = $exception; // not used
			$github_user = GithubUser::manufacture();
		}
		
		$this->getApiMapHelper()->applyDataToEntity($userInformation->getResponse(), $github_user);
    
    $github_access_token = new GithubAccessToken();
    $github_access_token->setToken($error->getAccessToken());
    
    $github_rate_limit = GithubRateLimit::manufacture();
    
    $github_rate_limit->setAccessToken($github_access_token);
    $github_access_token->setRateLimit($github_rate_limit);
    
    $github_access_token->setUser($github_user);
    $github_user->setAccessToken($github_access_token);
    
    $github_rate_limit->forceUpdateFromApi();

    $em->persist($github_rate_limit);
    $em->persist($github_access_token);
    $em->persist($github_user);
    $em->flush();
   
    $original_username = $userInformation->getNickname();
    $i = 0;
    $testName = $original_username;

    do {
        $user_exists = $userManager->findUserByUsername($testName);
    } while ($user_exists !== null && $i < 30 && $testName = $original_username.++$i);

    $username = $testName;
    $user->setUsername($username);
    $user->setPlainPassword(sha1(microtime(true)));
    $user->setEmail($userInformation->getEmail());
    $user->setEnabled(1);
    $userManager->updateUser($user);
    
    $this->container->get("hwi_oauth.account.connector")->connect($user, $userInformation);
    
    $this->authenticateUser($user, null, null);
    
    return $this->container->get('templating')->renderResponse('HWIOAuthBundle:Connect:registration_success.html.twig', array(
          'userInformation' => $userInformation,
        ));
  }

  /**
   * Authenticate a user with Symfony Security
   *
   * @param UserInterface $user
   */
  protected function authenticateUser(UserInterface $user, $resourceOwnerName, $accessToken) {
    try {
      $thing = $this->container->get('hwi_oauth.user_checker');
      $thing->checkPostAuth($user);
    } catch (AccountStatusException $e) {
      // Don't authenticate locked, disabled or expired users
      return;
    }


    $providerKey = $this->container->getParameter('hwi_oauth.firewall_name');
    $token = null;
    if ($this->error) {
      $token = new OAuthToken($this->error->getAccessToken(), $user->getRoles());
      $token->setResourceOwnerName($this->error->getResourceOwnerName());
      $token->setUser($user);
      $token->setAuthenticated(true);
    } else {
      $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
    }

    $this->container->get('security.context')->setToken($token);
  }
	
	/**
	 * Returns an instance of ApiMapHelper for processing data into entities
	 * 
	 * @return \RRaven\Bundle\LaughingbearBundle\Helper\ApiMapHelper
	 */
	private function getApiMapHelper() {
		return 
			(
				$this->_api_map_helper
					? $this->_api_map_helper
					: $this->_api_map_helper = $this->container->get("rraven.helper.apimap")
			)
		;
	}
	
	/**
	 * Override the ApiMapHelper used in the class, in stead of using the 
	 * container service.
	 * 
	 * Useful for testing.
	 * 
	 * @param \RRaven\Bundle\LaughingbearBundle\Helper\ApiMapHelper $api_map_helper
	 * @return \RRaven\Bundle\HWIOAuthOverrideBundle\Controller\ConnectController
	 */
	public function setApiMapHelper($api_map_helper) {
		$this->_api_map_helper = $api_map_helper;
		
		return $this;
	}

}

?>

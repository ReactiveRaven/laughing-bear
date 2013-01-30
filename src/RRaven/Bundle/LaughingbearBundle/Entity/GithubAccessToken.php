<?php

namespace RRaven\Bundle\LaughingbearBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Buzz\Browser;
use RRaven\Bundle\LaughingbearBundle\Utility\Buzz\Message\Factory\Factory;
use RRaven\Bundle\LaughingbearBundle\Utility\Buzz\Message\Response\Response;
use RRaven\Bundle\LaughingbearBundle\Utility\Exception\RateLimitException;

/**
 * @ORM\Entity
 */
class GithubAccessToken extends AbstractContainerAwareEntity {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="string", length=255)
   */
  protected $token;

  /**
   * @ORM\OneToOne(targetEntity="GithubRateLimit", fetch="EAGER", inversedBy="accessToken")
   */
  protected $rateLimit;

  /**
   * @ORM\OneToOne(targetEntity="GithubUser", fetch="LAZY", inversedBy="accessToken")
   */
  protected $user;
	
	/**
	 * @var $_browser Browser
	 */
	private $_browser;

  /**
   * Manufacture an instance
   * @return \RRaven\Bundle\LaughingbearBundle\Entity\GithubAccessToken
   */
  public static function manufacture() {
    return new GithubAccessToken();
  }

  /**
   * Returns true if the token will run out of usages before the refresh time
   * arrives if the current rate carries on.
   * 
   * Does it fairly simply as ratios of time vs used.
   * 
   * @return boolean true if too fast
   */
  public function isBeingCalledTooFast() {
    return $this->getRateLimit()->isBeingCalledTooFast();
  }

  /**
   * @return GithubUser
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * @return GithubRateLimit
   */
  public function getRateLimit() {
    return $this->rateLimit;
  }

  /**
   * Access token used with the service
   * @return string
   */
  public function getToken() {
    return $this->token;
  }

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set token
   *
   * @param string $token
   * @return GithubAccessToken
   */
  public function setToken($token) {
    $this->token = $token;
    return $this;
  }

  /**
   * Set rateLimit
   *
   * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubRateLimit $rateLimit
   * @return GithubAccessToken
   */
  public function setRateLimit(\RRaven\Bundle\LaughingbearBundle\Entity\GithubRateLimit $rateLimit = null) {
    $this->rateLimit = $rateLimit;
    return $this;
  }

  /**
   * Set user
   *
   * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubUser $user
   * @return GithubAccessToken
   */
  public function setUser(\RRaven\Bundle\LaughingbearBundle\Entity\GithubUser $user = null) {
    $this->user = $user;
    return $this;
  }
	
	/**
	 * Internal function to generate a (modified) browser on demand
	 * 
	 * @throws RateLimitException if you are making requests too fast if you need to make fewer requests
	 * @return Browser
	 */
	private function getBuzzBrowser()
	{
		if ($this->getRateLimit()->isBeingCalledTooFast())
		{
			throw new RateLimitException("Access token is being called too fast");
		}
		return 
			(
				$this->_browser
					? $this->_browser
					: $this->_browser = $this->getContainer()->get('rraven.buzz.github')
			)
		;
	}
	
	/**
	 * Set the browser object used for making requests.
	 * 
	 * Useful for overriding default behaviour during testing.
	 * 
	 * @param mixed $browser to use for making requests
	 * @return GithubAccessToken
	 */
	public function setBuzzBrowser($browser)
	{
		$this->_browser = $browser;
		
		return $this;
	}
    
    /**
     * Tidies up a URL to include the necessary github url if missing
     * 
     * @param string $url
     * @return string tidied url
     */
    private function tidyUrl($url)
    {
        if (!preg_match("#^https?://#", $url)) {
            $url = "https://api.github.com/" . ltrim($url, "/");
        }
        return $url;
    }
	
	/**
	 * Makes a GET request via Buzz
	 * 
	 * Note: the authorisation header is already set.
	 * 
	 * @param string $url
	 * @param string[] $headers
	 * @return Response
	 * @throws \InvalidArgumentException
	 * @throws RateLimitException if you are making requests too fast 
	 */
  public function makeGetRequest($url, $headers = array())
	{
		if (!is_array($headers))
		{
			throw new \InvalidArgumentException("Expected headers to be an array");
		}
		$headers[] = "Authorization: token " . $this->getToken();
		$response = $this->getBuzzBrowser()->get($this->tidyUrl($url), $headers);
		/* @var $response Response */
		$this->getRateLimit()->setRemaining($response->getRateLimitRemaining());
		return $response;
	}
	
	/**
	 * Makes a POST request via Buzz
	 * 
	 * Note: the authorisation header is already set.
	 * 
	 * @param string $url
	 * @param string[] $headers
	 * @param mixed $content
	 * @return Response
	 * @throws \InvalidArgumentException
	 * @throws RateLimitException if you are making requests too fast
	 */
	public function makePostRequest($url, $headers = array(), $content = null)
	{
		if (!is_array($headers))
		{
			throw new \InvalidArgumentException("Expected headers to be an array");
		}
		$headers[] = "Authorization: token " . $this->getToken();
		$response = $this->getBuzzBrowser()->post($this->tidyUrl($url), $headers, $content);
		/* @var $response Response */
		$this->getRateLimit()->setRemaining($response->getRateLimitRemaining());
		return $response;
	}
	
	/**
	 * Makes a DELETE request via Buzz
	 * 
	 * Note: the authorisation header is already set.
	 * 
	 * @param string $url
	 * @param string[] $headers
	 * @param mixed $content
	 * @return Response
	 * @throws \InvalidArgumentException
	 * @throws RateLimitException if you are making requests too fast
	 */
	public function makeDeleteRequest($url, $headers = array(), $content = null)
	{
		if (!is_array($headers))
		{
			throw new \InvalidArgumentException("Expected headers to be an array");
		}
		$headers[] = "Authorization: token " . $this->getToken();
		$response = $this->getBuzzBrowser()->delete($this->tidyUrl($url), $headers, $content);
		/* @var $response Response */
		$this->getRateLimit()->setRemaining($response->getRateLimitRemaining());
		return $response;
	}
	
	/**
	 * Makes a HEAD request via Buzz
	 * 
	 * Note: the authorisation header is already set.
	 * 
	 * @param string $url
	 * @param string[] $headers
	 * @return Response
	 * @throws \InvalidArgumentException
	 * @throws RateLimitException if you are making requests too fast
	 */
	public function makeHeadRequest($url, $headers = array())
	{
		if (!is_array($headers))
		{
			throw new \InvalidArgumentException("Expected headers to be an array");
		}
		$headers[] = "Authorization: token " . $this->getToken();
		$response = $this->getBuzzBrowser()->head($this->tidyUrl($url), $headers);
		/* @var $response Response */
		$this->getRateLimit()->setRemaining($response->getRateLimitRemaining());
		return $response;
	}
	
	/**
	 * Makes a PUT request via Buzz
	 * 
	 * Note: the authorisation header is already set.
	 * 
	 * @param string $url
	 * @param string[] $headers
	 * @param mixed $content
	 * @return Response
	 * @throws \InvalidArgumentException
	 * @throws RateLimitException if you are making requests too fast
	 */
	public function makePutRequest($url, $headers = array(), $content = null)
	{
		if (!is_array($headers))
		{
			throw new \InvalidArgumentException("Expected headers to be an array");
		}
		$headers[] = "Authorization: token " . $this->getToken();
		$response = $this->getBuzzBrowser()->put($this->tidyUrl($url), $headers, $content);
		/* @var $response Response */
		$this->getRateLimit()->setRemaining($response->getRateLimitRemaining());
		return $response;
	}
	
	/**
	 * Makes a PATCH request via Buzz
	 * 
	 * Note: the authorisation header is already set.
	 * 
	 * @param string $url
	 * @param string[] $headers
	 * @param mixed $content
	 * @return Response
	 * @throws \InvalidArgumentException
	 * @throws RateLimitException if you are making requests too fast
	 */
	public function makePatchRequest($url, $headers = array(), $content = null)
	{
		if (!is_array($headers))
		{
			throw new \InvalidArgumentException("Expected headers to be an array");
		}
		$headers[] = "Authorization: token " . $this->getToken();
		$response = $this->getBuzzBrowser()->patch($this->tidyUrl($url), $headers, $content);
		/* @var $response Response */
		$this->getRateLimit()->setRemaining($response->getRateLimitRemaining());
		return $response;
	}

}
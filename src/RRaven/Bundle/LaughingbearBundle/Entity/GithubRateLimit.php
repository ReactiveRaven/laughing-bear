<?php

namespace RRaven\Bundle\LaughingbearBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
		DateTime,
		RRaven\Bundle\LaughingbearBundle\Annotations\Api;

/**
 * @ORM\Entity
 */
class GithubRateLimit extends AbstractContainerAwareEntity {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\OneToOne(targetEntity="GithubAccessToken", fetch="LAZY", mappedBy="rateLimit")
	 */
	protected $accessToken;

	/**
	 * @ORM\Column(type="integer", name="`limit`")
	 * @Api\Map(keys={"limit"})
	 */
	protected $limit;

	/**
	 * @ORM\Column(type="integer")
	 * @Api\Map(keys={"remaining"})
	 */
	protected $remaining;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $refreshTime;

	private $_api_map_helper;

	/**
	 *
	 * @ORM\Column(type="integer")
	 */
	protected $refreshInterval;

	public function __construct() {

		// Set defaults
		$this->setRefreshInterval(3600); // 60 * 60 = 1 hour
		$this->setLimit(5000);
		$this->getRefreshTime();
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
		$remainingRatio = $this->getRemaining() / $this->getLimit();
		$now = time();
		$refreshTimestamp = $this->getRefreshTime()->getTimestamp();
		$refreshInterval = $this->getRefreshInterval();

		$timestamp = $refreshTimestamp - $now - $refreshInterval;
		$timeRatio = $timestamp / $refreshInterval;

		return $remainingRatio < $timeRatio;
	}
	
	/**
	 * @return \RRaven\Bundle\LaughingbearBundle\Helper\ApiMapHelper
	 */
	private function getApiMapHelper()
	{
		return
			(
				$this->_api_map_helper
					? $this->_api_map_helper
					: $this->_api_map_helper = $this->getContainer()->get("rraven.helper.apimap")
			)
		;
	}

	/**
	 * Attempts to update the RateLimit using it's assigned AccessToken.
	 * 
	 * @throws \InvalidArgumentException if we don't have an AccessToken yet
	 * @throws \UnexpectedValueException if the response is not valid JSON
	 * 
	 */
	public function forceUpdateFromApi() {
		$browser = $this->getContainer()->get('rraven.buzz.github');
		
		if (!$this->getAccessToken()) {
			throw new \InvalidArgumentException("No AccessToken set for this RateLimit");
		}
		
		if (!$this->getApiMapHelper()) {
			throw new \InvalidArgumentException("No ApiMapHelper set for this RateLimit");
		}
		
		try
		{
			$response =
				json_decode(
				$browser
					->get(
						"https://api.github.com/rate_limit", array("Authorization: token " . $this->getAccessToken()->getToken())
					)->getContent(), true
				)
			;
			
			$this->getApiMapHelper()->applyDataToEntity($response["rate"], $this);
		}
		catch (Exception $e) {
			$e = $e; // not used, shut up netbeans!
			throw new \Symfony\Component\Serializer\Exception\UnexpectedValueException("API response not valid JSON");
		}
	}

	/**
	 * Manufacture an instance
	 * @return GithubRateLimit
	 */
	public static function manufacture() {
		return new GithubRateLimit();
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
	 * Set refreshInterval
	 *
	 * @param integer $refreshInterval
	 * @return GithubRateLimit
	 */
	public function setRefreshInterval($refreshInterval) {
		$this->refreshInterval = $refreshInterval;
		return $this;
	}

	/**
	 * Get refreshInterval
	 *
	 * @return integer 
	 */
	public function getRefreshInterval() {
		return $this->refreshInterval;
	}

	/**
	 * Set accessToken
	 *
	 * @param GithubAccessToken $accessToken
	 * @return GithubRateLimit
	 */
	public function setAccessToken(GithubAccessToken $accessToken = null) {
		$this->accessToken = $accessToken;
		return $this;
	}

	/**
	 * Get accessToken
	 *
	 * @return GithubAccessToken 
	 */
	public function getAccessToken() {
		return $this->accessToken;
	}

	/**
	 * Set refreshTime
	 *
	 * @param DateTime $refreshTime
	 * @return GithubRateLimit
	 */
	public function setRefreshTime(DateTime $refreshTime) {
		$this->refreshTime = $refreshTime;
		return $this;
	}

	/**
	 * Get refreshTime
	 *
	 * @return DateTime 
	 */
	public function getRefreshTime() {

		$now = time();
		$interval = $this->getRefreshInterval();
		$timestamp = $this->refreshTime ? $this->refreshTime->getTimestamp() : 0;

		// Has the ratelimit already refreshed?
		if ($now > $timestamp) {

			// Great! But we don't know if it refreshed recently. If its really old,
			// skip to this hour.
			if (($now - $timestamp) > 100 * $interval) {
				$timestamp = strtotime(date("Y-m-d H:00:00"));
			}

			// Keep skipping forward in $interval-s until we pass $now
			while ($timestamp < $now) {
				$timestamp += $interval;
			}

			$newRefreshTime = new DateTime();
			$newRefreshTime->setTimestamp($timestamp);

			// Update the refresh time
			$this->setRefreshTime($newRefreshTime);
			// We have a new allocation now! Yay :)
			$this->setRemaining($this->getLimit());
		}

		// Oh yeah, you wanted the refresh time right?
		return $this->refreshTime;
	}

	/**
	 * Set maximum available during a fresh allocation
	 *
	 * @param integer $limit
	 * @return GithubRateLimit
	 */
	public function setLimit($limit) {
		$this->limit = $limit;
		return $this;
	}

	/**
	 * Get maximum available during a fresh allocation
	 *
	 * @return integer 
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * Set remaining
	 *
	 * @param integer $remaining
	 * @return GithubRateLimit
	 */
	public function setRemaining($remaining) {
		$this->remaining = $remaining;
		if ($remaining === null || $remaining === false) {
			$this->forceUpdateFromApi();
		}
		return $this;
	}

	/**
	 * Get remaining
	 *
	 * @return integer 
	 */
	public function getRemaining() {
		return $this->remaining;
	}

}
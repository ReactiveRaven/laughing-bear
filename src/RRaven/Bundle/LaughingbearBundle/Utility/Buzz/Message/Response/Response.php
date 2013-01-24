<?php

namespace RRaven\Bundle\LaughingbearBundle\Utility\Buzz\Message\Response;

use Buzz\Message\Response as BaseResponse;

class Response extends BaseResponse {
	private $_pagination = null;
	
	public function getPagination()
	{
		if ($this->_pagination !== null)
		{
			return $this->_pagination;
		}
		
		$header_link = $this->getHeader("Link");
		
		$pagination = array();
		if ($header_link !== null) {
			$matches = array();
			preg_match_all("/<(?P<url>[^>]+)>; rel=\"(?P<key>[^\"]+)\"/", $header_link, $matches);
			$pagination = 
				array_merge(
					$pagination, 
					array_combine($matches["key"], $matches["url"])
				)
			;
		}
		
		return $this->_pagination = $pagination;
	}
	
	public function getNextLink()
	{
		$pagination = $this->getPagination();
		return isset($pagination["next"]) ? $pagination["next"] : null;
	}
	
	public function getPrevLink()
	{
		$pagination = $this->getPagination();
		return isset($pagination["prev"]) ? $pagination["prev"] : null;
	}
	
	public function getFirstLink()
	{
		$pagination = $this->getPagination();
		return isset($pagination["first"]) ? $pagination["first"] : null;
	}
	
	public function getLastLink()
	{
		$pagination = $this->getPagination();
		return isset($pagination["last"]) ? $pagination["last"] : null;
	}
	
	public function getRateLimitRemaining()
	{
		return $this->getHeader("X-RateLimit-Remaining");
	}
	
}
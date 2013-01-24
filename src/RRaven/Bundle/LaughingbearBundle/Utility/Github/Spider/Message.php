<?php

namespace RRaven\Bundle\LaughingbearBundle\Utility\Github\Spider;

/**
 * Description of GithubSpiderMessage
 *
 * @param string $action
 * @param string subject
 * @param string token
 * @param mixed $object
 * 
 * @author godfred7
 */
class Message {
  private $token;
  private $action;
  private $subject;
  private $object;
  private $inflatedObject = null;
  
  /**
   * @param string $action
   * @param string $subject
   * @param string $token
   * @param mixed $object
   */
  public function __construct($action, $subject, $token, $object = null) {
    $this->action = $action;
    $this->token = $token;
    $this->object = serialize($object);
    $this->subject = $subject;
    $this->inflatedObject = $object;
  }
	
	public function __sleep() {
		return array("token", "action", "subject", "object");
	}
  
  public function getToken()
  {
    return $this->token;
  }
  
  public function getAction()
  {
    return $this->action;
  }
  
  public function getSubject()
  {
    return $this->subject;
  }
  
  public function getObject()
  {
    return 
      (
        $this->inflatedObject 
          ? $this->inflatedObject 
          : $this->inflatedObject = unserialize($this->object)
      )
    ;
  }
}
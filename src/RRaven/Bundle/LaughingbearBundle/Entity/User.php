<?php

namespace RRaven\Bundle\LaughingbearBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $githubId;
		
		/**
		 * @return \RRaven\Bundle\LaughingbearBundle\Entity\User
		 */
		public static function manufacture()
		{
			return new User();
		}

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
    
    public function setGithubId($newId)
    {
      $this->githubId = $newId;
    }
    
    public function getGithubId()
    {
      return $this->githubId;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
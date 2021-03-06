<?php

namespace RRaven\Bundle\LaughingbearBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use RRaven\Bundle\LaughingbearBundle\Annotations\Api;

/**
 * @ORM\Entity
 */
class GithubRepositoryPermissions {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
		
		/**
		 * @ORM\Column(type="boolean")
		 * @Api\Map(keys={"push"})
		 */
		protected $push;
		
		/**
		 * @ORM\Column(type="boolean")
		 * @Api\Map(keys={"pull"})
		 */
		protected $pull;
		
		/**
		 * @ORM\Column(type="boolean")
		 * @Api\Map(keys={"admin"})
		 */
		protected $admin;
		
		/**
		 * @ORM\Column(name="`user`")
		 * @ORM\ManyToOne(targetEntity="GithubUser", fetch="EAGER")
		 */
		protected $user;
		
		/**
		 * @ORM\ManyToOne(targetEntity="GithubRepository", fetch="EAGER", inversedBy="permissions")
		 */
		protected $repository;
		
		/**
		 * @return \RRaven\Bundle\LaughingbearBundle\Entity\GithubRepositoryPermissions
		 */
		public static function manufacture()
		{
			return new GithubRepositoryPermissions();
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

    /**
     * Set push
     *
     * @param boolean $push
     * @return GithubRepositoryPermissions
     */
    public function setPush($push)
    {
        $this->push = $push;
        return $this;
    }

    /**
     * Get push
     *
     * @return boolean 
     */
    public function getPush()
    {
        return $this->push;
    }

    /**
     * Set pull
     *
     * @param boolean $pull
     * @return GithubRepositoryPermissions
     */
    public function setPull($pull)
    {
        $this->pull = $pull;
        return $this;
    }

    /**
     * Get pull
     *
     * @return boolean 
     */
    public function getPull()
    {
        return $this->pull;
    }

    /**
     * Set admin
     *
     * @param boolean $admin
     * @return GithubRepositoryPermissions
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
        return $this;
    }

    /**
     * Get admin
     *
     * @return boolean 
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return GithubRepositoryPermissions
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set repository
     *
     * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $repository
     * @return GithubRepositoryPermissions
     */
    public function setRepository(\RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $repository = null)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * Get repository
     *
     * @return RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository 
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
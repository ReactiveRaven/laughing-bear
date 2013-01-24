<?php

namespace RRaven\Bundle\LaughingbearBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use RRaven\Bundle\LaughingbearBundle\Annotations\Api;

use \DateTime;

/**
 * @ORM\Entity
 */
class GithubOrganization
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
		/**
		 * @ORM\Column(type="string", length=255)
		 * @Api\Map(keys={"type"})
		 */
		protected $type;
		
		/**
		 * @ORM\Column(type="string", length=255, nullable=true)
		 * @Api\Map(keys={"email"})
		 */
		protected $email;
		
		/**
		 * @ORM\Column(type="integer")
		 * @Api\Map(keys={"following"})
		 */
		protected $following;
		
		/**
		 * @ORM\Column(type="string", length=2000, nullable=true)
		 * @Api\Map(keys={"html_url"})
		 */
		protected $html_url;
		
		/**
		 * @ORM\Column(type="string", length=255, nullable=true)
		 * @Api\Map(keys={"location"})
		 */
		protected $location;
		
		/**
		 * @ORM\Column(type="string", length=2000, nullable=true)
		 * @Api\Map(keys={"blog"})
		 */
		protected $blog;
		
		/**
		 * @ORM\Column(type="integer")
		 * @Api\Map(keys={"public_gists"})
		 */
		protected $public_gists;
		
		/**
		 * @ORM\Column(type="string", length=255)
		 * @Api\Map(keys={"nae"})
		 */
		protected $name;
		
		/**
		 * @ORM\Column(type="string", length=255)
		 * @Api\Map(keys={"company"})
		 */
		protected $company;
		
		/**
		 * @ORM\Column(type="string", length=2000)
		 * @Api\Map(keys={"url"})
		 */
		protected $url;
		
		/**
		 * @ORM\Column(type="string", length=255)
		 * @Api\Map(keys={"login"})
		 */
		protected $login;
		
		/**
		 * @ORM\Column(type="integer")
		 * @Api\Map(keys={"followers"})
		 */
		protected $followers;
		
		/**
		 * @ORM\Column(type="string", length=2000)
		 * @Api\Map(keys={"avatar_url"})
		 */
		protected $avatar_url;
		
		/**
		 * @ORM\Column(type="integer")
		 * @Api\Map(keys={"id"})
		 */
		protected $github_id;
		
		/**
		 * @ORM\Column(type="integer")
		 * @Api\Map(keys={"public_repos"})
		 */
		protected $public_repos;
		
		/**
		 * @ORM\OneToMany(targetEntity="GithubRepository", mappedBy="organization")
		 */
		protected $repositories;
		
		/**
		 * @ORM\Column(type="datetime")
		 * @Api\Map(keys={"created_at"})
		 */
		protected $created_at;
    
    /**
     * Manufacture an instance
     * @return \RRaven\Bundle\LaughingbearBundle\Entity\GithubUser
     */
    public static function manufacture()
    {
      return new GithubUser();
    }
    public function __construct()
    {
        $this->repositories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set type
     *
     * @param string $type
     * @return GithubOrganization
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return GithubOrganization
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set following
     *
     * @param integer $following
     * @return GithubOrganization
     */
    public function setFollowing($following)
    {
        $this->following = $following;
        return $this;
    }

    /**
     * Get following
     *
     * @return integer 
     */
    public function getFollowing()
    {
        return $this->following;
    }

    /**
     * Set html_url
     *
     * @param string $htmlUrl
     * @return GithubOrganization
     */
    public function setHtmlUrl($htmlUrl)
    {
        $this->html_url = $htmlUrl;
        return $this;
    }

    /**
     * Get html_url
     *
     * @return string 
     */
    public function getHtmlUrl()
    {
        return $this->html_url;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return GithubOrganization
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set blog
     *
     * @param string $blog
     * @return GithubOrganization
     */
    public function setBlog($blog)
    {
        $this->blog = $blog;
        return $this;
    }

    /**
     * Get blog
     *
     * @return string 
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * Set public_gists
     *
     * @param integer $publicGists
     * @return GithubOrganization
     */
    public function setPublicGists($publicGists)
    {
        $this->public_gists = $publicGists;
        return $this;
    }

    /**
     * Get public_gists
     *
     * @return integer 
     */
    public function getPublicGists()
    {
        return $this->public_gists;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return GithubOrganization
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set company
     *
     * @param string $company
     * @return GithubOrganization
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * Get company
     *
     * @return string 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return GithubOrganization
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set login
     *
     * @param string $login
     * @return GithubOrganization
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set followers
     *
     * @param integer $followers
     * @return GithubOrganization
     */
    public function setFollowers($followers)
    {
        $this->followers = $followers;
        return $this;
    }

    /**
     * Get followers
     *
     * @return integer 
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Set avatar_url
     *
     * @param string $avatarUrl
     * @return GithubOrganization
     */
    public function setAvatarUrl($avatarUrl)
    {
        $this->avatar_url = $avatarUrl;
        return $this;
    }

    /**
     * Get avatar_url
     *
     * @return string 
     */
    public function getAvatarUrl()
    {
        return $this->avatar_url;
    }

    /**
     * Set github_id
     *
     * @param integer $githubId
     * @return GithubOrganization
     */
    public function setGithubId($githubId)
    {
        $this->github_id = $githubId;
        return $this;
    }

    /**
     * Get github_id
     *
     * @return integer 
     */
    public function getGithubId()
    {
        return $this->github_id;
    }

    /**
     * Set public_repos
     *
     * @param integer $publicRepos
     * @return GithubOrganization
     */
    public function setPublicRepos($publicRepos)
    {
        $this->public_repos = $publicRepos;
        return $this;
    }

    /**
     * Get public_repos
     *
     * @return integer 
     */
    public function getPublicRepos()
    {
        return $this->public_repos;
    }

    /**
     * Set created_at
     *
     * @param DateTime $createdAt
     * @return GithubOrganization
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->created_at = $createdAt;
        return $this;
    }

    /**
     * Get created_at
     *
     * @return DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Add repositories
     *
     * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $repositories
     * @return GithubOrganization
     */
    public function addRepositorie(\RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $repositories)
    {
        $this->repositories[] = $repositories;
        return $this;
    }

    /**
     * Remove repositories
     *
     * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $repositories
     */
    public function removeRepositorie(\RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $repositories)
    {
        $this->repositories->removeElement($repositories);
    }

    /**
     * Get repositories
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getRepositories()
    {
        return $this->repositories;
    }
}
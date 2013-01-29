<?php

namespace RRaven\Bundle\LaughingbearBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use RRaven\Bundle\LaughingbearBundle\Annotations\Api;

/**
 * @ORM\Entity 
*/
class GithubRepository
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
				
		/**
		 * @ORM\Column(type="integer")
		 * @Api\Map(keys={"id"})
		 */
		protected $github_id;
		
		/**
		 * @ORM\Column(type="datetime")
		 * @Api\Map(keys={"pushed_at"})
		 */
		protected $pushed_at;
		
		/**
		 * @ORM\Column(type="integer")
		 * @Api\Map(keys={"forks"})
		 */
		protected $forks;
		
		/**
		 * @ORM\Column(type="boolean")
		 * @Api\Map(keys={"has_issues"})
		 */
		protected $has_issues;
		
		/**
		 * @ORM\Column(type="string", length=255)
		 * @Api\Map(keys={"full_name"})
		 */
		protected $full_name;
		
		/**
		 * @ORM\Column(type="integer")
		 * @Api\Map(keys={"forks_count"})
		 */
		protected $forks_count;
		
		/**
		 * @ORM\Column(type="boolean")
		 * @Api\Map(keys={"has_downloads"})
		 */
		protected $has_downloads;
		
		/**
		 * @ORM\Column(type="string", length=255)
		 * @Api\Map(keys={"svn_url"})
		 */
		protected $svn_url;
		
		/**
		 * @ORM\Column(type="string", length=500, nullable=true)
		 * @Api\Map(keys={"mirror_url"})
		 */
		protected $mirror_url;
		
		/**
		 * @ORM\Column(type="string", length=500, nullable=true)
		 * @Api\Map(keys={"homepage"})
		 */
		protected $homepage;
		
		/**
		 * @ORM\ManyToOne(targetEntity="GithubUser", fetch="LAZY")
         * @Api\Link(url = "owner/url", id = "owner/id", target = "github_id", required = "true", auto_import = "true")
		 */
		protected $user;
		
		/**
		 * @ORM\ManyToOne(targetEntity="GithubRepository", inversedBy="child_repositories", fetch="LAZY")
         * @Api\Link(url = "parent/url", id = "parent/id", target = "github_id", required = "true", auto_import = "true")
		 */
		protected $parent;
		
		/**
		 * @ORM\OneToMany(targetEntity="GithubRepository", mappedBy="parent_repository", fetch="LAZY")
		 */
		protected $child_repositories;
		
		/**
		 * @ORM\ManyToOne(targetEntity="GithubRepository", inversedBy="forked_repositories", fetch="LAZY")
         * @Api\Link(url = "source/url", id = "source/id", target = "github_id", required = "true", auto_import = "true")
		 */
		protected $source;
		
		/**
		 * @ORM\OneToMany(targetEntity="GithubRepository", mappedBy="source_repository", fetch="LAZY")
		 */
		protected $forked_repositories;
		
		/**
		 * @ORM\ManyToOne(targetEntity="GithubOrganization", inversedBy="repositories", fetch="LAZY")
     * @Api\Link(url = "organization/url", id = "organization/id", target = "github_id", required = "true", auto_import = "true")
		 */
		protected $organization;
		
		/**
		 * @ORM\OneToMany(targetEntity="GithubRepositoryPermissions", fetch="LAZY", mappedBy="repository")
         * @Api\Apply(key="permissions")
		 */
		protected $permissions;

		/**
		 * @ORM\Column(type="string", length=255, nullable=true)
		 * @Api\Map(keys={"language"})
		 */
		protected $language;
		
		/**
		 * @ORM\Column(type="string", length=255)
		 * @Api\Map(keys={"git_url"})
		 */
		protected $git_url;
		
		/**
		 * @ORM\Column(type="datetime")
		 * @Api\Map(keys={"created_at"})
		 */
		protected $created_at;
		
		/**
		 * @ORM\Column(type="boolean")
		 * @Api\Map(keys={"has_wiki"})
		 */
		protected $has_wiki;
		
		/**
		 * @ORM\Column(type="integer")
		 * @Api\Map(keys={"size"})
		 */
		protected $size;
		
		/**
		 * @ORM\Column(type="boolean")
		 * @Api\Map(keys={"fork"})
		 */
		protected $fork;
		
		/**
		 * @ORM\Column(type="string", length=500, nullable=true)
		 * @Api\Map(keys={"description"})
		 */
		protected $description;
		
		/**
		 * @ORM\Column(type="string", length=255)
		 * @Api\Map(keys={"clone_url"})
		 */
		protected $clone_url;
		
		/**
		 * @ORM\Column(type="string", length=255)
		 * @Api\Map(keys={"html_url"})
		 */
		protected $html_url;
		
		/**
		 * @ORM\Column(type="integer")
		 * @Api\Map(keys={"watchers"})
		 */
		protected $watchers;
		
		/**
		 * @ORM\Column(type="integer")
		 * @Api\Map(keys={"watchers_count"})
		 */
		protected $watchers_count;
		
		/**
		 * @ORM\Column(name="`name`", type="string", length=255)
		 * @Api\Map(keys={"name"})
		 */
		protected $name;
		
		/**
		 * @ORM\Column(type="string", length=255)
		 * @Api\Map(keys={"url"})
		 */
		protected $url;
		
		/**
		 * @ORM\Column(type="string", length=255)
		 * @Api\Map(keys={"ssh_url"})
		 */
		protected $ssh_url;
		
		/**
		 * @ORM\Column(type="boolean")
		 * @Api\Map(keys={"private"})
		 */
		protected $private;
		
		/**
		 * @ORM\Column(type="datetime")
		 * @Api\Map(keys={"updated_at"})
		 */
		protected $updated_at;
		
		/**
		 * @ORM\Column(type="integer")
		 * @Api\Map(keys={"open_issues"})
		 */
		protected $open_issues;
		
		/**
		 * @ORM\Column(type="integer", nullable=true)
		 * @Api\Map(keys={"open_issues_count"})
		 */
		protected $open_issues_count;
    
    /**
     * Manufacture an instance
     * @return \RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository
     */
    public static function manufacture()
    {
      return new GithubRepository();
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
     * Set pushed_at
     *
     * @param datetime $pushedAt
     * @return GithubRepository
     */
    public function setPushedAt($pushedAt)
    {
        $this->pushed_at = $pushedAt;
        return $this;
    }

    /**
     * Get pushed_at
     *
     * @return datetime 
     */
    public function getPushedAt()
    {
        return $this->pushed_at;
    }

    /**
     * Set forks
     *
     * @param integer $forks
     * @return GithubRepository
     */
    public function setForks($forks)
    {
        $this->forks = $forks;
        return $this;
    }

    /**
     * Get forks
     *
     * @return integer 
     */
    public function getForks()
    {
        return $this->forks;
    }

    /**
     * Set has_issues
     *
     * @param boolean $hasIssues
     * @return GithubRepository
     */
    public function setHasIssues($hasIssues)
    {
        $this->has_issues = $hasIssues;
        return $this;
    }

    /**
     * Get has_issues
     *
     * @return boolean 
     */
    public function getHasIssues()
    {
        return $this->has_issues;
    }

    /**
     * Set full_name
     *
     * @param string $fullName
     * @return GithubRepository
     */
    public function setFullName($fullName)
    {
        $this->full_name = $fullName;
        return $this;
    }

    /**
     * Get full_name
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * Set forks_count
     *
     * @param integer $forksCount
     * @return GithubRepository
     */
    public function setForksCount($forksCount)
    {
        $this->forks_count = $forksCount;
        return $this;
    }

    /**
     * Get forks_count
     *
     * @return integer 
     */
    public function getForksCount()
    {
        return $this->forks_count;
    }

    /**
     * Set has_downloads
     *
     * @param boolean $hasDownloads
     * @return GithubRepository
     */
    public function setHasDownloads($hasDownloads)
    {
        $this->has_downloads = $hasDownloads;
        return $this;
    }

    /**
     * Get has_downloads
     *
     * @return boolean 
     */
    public function getHasDownloads()
    {
        return $this->has_downloads;
    }

    /**
     * Set svn_url
     *
     * @param string $svnUrl
     * @return GithubRepository
     */
    public function setSvnUrl($svnUrl)
    {
        $this->svn_url = $svnUrl;
        return $this;
    }

    /**
     * Get svn_url
     *
     * @return string 
     */
    public function getSvnUrl()
    {
        return $this->svn_url;
    }

    /**
     * Set mirror_url
     *
     * @param string $mirrorUrl
     * @return GithubRepository
     */
    public function setMirrorUrl($mirrorUrl)
    {
        $this->mirror_url = $mirrorUrl;
        return $this;
    }

    /**
     * Get mirror_url
     *
     * @return string 
     */
    public function getMirrorUrl()
    {
        return $this->mirror_url;
    }

    /**
     * Set homepage
     *
     * @param string $homepage
     * @return GithubRepository
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
        return $this;
    }

    /**
     * Get homepage
     *
     * @return string 
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return GithubRepository
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set git_url
     *
     * @param string $gitUrl
     * @return GithubRepository
     */
    public function setGitUrl($gitUrl)
    {
        $this->git_url = $gitUrl;
        return $this;
    }

    /**
     * Get git_url
     *
     * @return string 
     */
    public function getGitUrl()
    {
        return $this->git_url;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     * @return GithubRepository
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
        return $this;
    }

    /**
     * Get created_at
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set has_wiki
     *
     * @param boolean $hasWiki
     * @return GithubRepository
     */
    public function setHasWiki($hasWiki)
    {
        $this->has_wiki = $hasWiki;
        return $this;
    }

    /**
     * Get has_wiki
     *
     * @return boolean 
     */
    public function getHasWiki()
    {
        return $this->has_wiki;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return GithubRepository
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set fork
     *
     * @param boolean $fork
     * @return GithubRepository
     */
    public function setFork($fork)
    {
        $this->fork = $fork;
        return $this;
    }

    /**
     * Get fork
     *
     * @return boolean 
     */
    public function getFork()
    {
        return $this->fork;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return GithubRepository
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set clone_url
     *
     * @param string $cloneUrl
     * @return GithubRepository
     */
    public function setCloneUrl($cloneUrl)
    {
        $this->clone_url = $cloneUrl;
        return $this;
    }

    /**
     * Get clone_url
     *
     * @return string 
     */
    public function getCloneUrl()
    {
        return $this->clone_url;
    }

    /**
     * Set html_url
     *
     * @param string $htmlUrl
     * @return GithubRepository
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
     * Set watchers
     *
     * @param integer $watchers
     * @return GithubRepository
     */
    public function setWatchers($watchers)
    {
        $this->watchers = $watchers;
        return $this;
    }

    /**
     * Get watchers
     *
     * @return integer 
     */
    public function getWatchers()
    {
        return $this->watchers;
    }

    /**
     * Set watchers_count
     *
     * @param integer $watchersCount
     * @return GithubRepository
     */
    public function setWatchersCount($watchersCount)
    {
        $this->watchers_count = $watchersCount;
        return $this;
    }

    /**
     * Get watchers_count
     *
     * @return integer 
     */
    public function getWatchersCount()
    {
        return $this->watchers_count;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return GithubRepository
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
     * Set url
     *
     * @param string $url
     * @return GithubRepository
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
     * Set ssh_url
     *
     * @param string $sshUrl
     * @return GithubRepository
     */
    public function setSshUrl($sshUrl)
    {
        $this->ssh_url = $sshUrl;
        return $this;
    }

    /**
     * Get ssh_url
     *
     * @return string 
     */
    public function getSshUrl()
    {
        return $this->ssh_url;
    }

    /**
     * Set private
     *
     * @param boolean $private
     * @return GithubRepository
     */
    public function setPrivate($private)
    {
        $this->private = $private;
        return $this;
    }

    /**
     * Get private
     *
     * @return boolean 
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Set updated_at
     *
     * @param datetime $updatedAt
     * @return GithubRepository
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return datetime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set open_issues
     *
     * @param integer $openIssues
     * @return GithubRepository
     */
    public function setOpenIssues($openIssues)
    {
        $this->open_issues = $openIssues;
        return $this;
    }

    /**
     * Get open_issues
     *
     * @return integer 
     */
    public function getOpenIssues()
    {
        return $this->open_issues;
    }

    /**
     * Set open_issues_count
     *
     * @param integer $openIssuesCount
     * @return GithubRepository
     */
    public function setOpenIssuesCount($openIssuesCount)
    {
        $this->open_issues_count = $openIssuesCount;
        return $this;
    }

    /**
     * Get open_issues_count
     *
     * @return integer 
     */
    public function getOpenIssuesCount()
    {
        return $this->open_issues_count;
    }

    /**
     * Set user
     *
     * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubUser $user
     * @return GithubRepository
     */
    public function setUser(\RRaven\Bundle\LaughingbearBundle\Entity\GithubUser $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return RRaven\Bundle\LaughingbearBundle\Entity\GithubUser 
     */
    public function getUser()
    {
        return $this->user;
    }
		
		/**
		 * Constructs a GithubRepository
		 */
    public function __construct()
    {
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add permissions
     *
     * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubRepositoryPermissions $permissions
     * @return GithubRepository
     */
    public function addPermission(\RRaven\Bundle\LaughingbearBundle\Entity\GithubRepositoryPermissions $permissions)
    {
        $this->permissions[] = $permissions;
        return $this;
    }

    /**
     * Remove permissions
     *
     * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubRepositoryPermissions $permissions
     */
    public function removePermission(\RRaven\Bundle\LaughingbearBundle\Entity\GithubRepositoryPermissions $permissions)
    {
        $this->permissions->removeElement($permissions);
    }

    /**
     * Get permissions
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Add child_repositories
     *
     * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $childRepositories
     * @return GithubRepository
     */
    public function addChildRepositorie(\RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $childRepositories)
    {
        $this->child_repositories[] = $childRepositories;
        return $this;
    }

    /**
     * Remove child_repositories
     *
     * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $childRepositories
     */
    public function removeChildRepositorie(\RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $childRepositories)
    {
        $this->child_repositories->removeElement($childRepositories);
    }

    /**
     * Get child_repositories
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getChildRepositories()
    {
        return $this->child_repositories;
    }

    /**
     * Add forked_repositories
     *
     * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $forkedRepositories
     * @return GithubRepository
     */
    public function addForkedRepositorie(\RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $forkedRepositories)
    {
        $this->forked_repositories[] = $forkedRepositories;
        return $this;
    }

    /**
     * Remove forked_repositories
     *
     * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $forkedRepositories
     */
    public function removeForkedRepositorie(\RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $forkedRepositories)
    {
        $this->forked_repositories->removeElement($forkedRepositories);
    }

    /**
     * Get forked_repositories
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getForkedRepositories()
    {
        return $this->forked_repositories;
    }

    /**
     * Set parent
     *
     * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $parent
     * @return GithubRepository
     */
    public function setParent(\RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent
     *
     * @return RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set source
     *
     * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $source
     * @return GithubRepository
     */
    public function setSource(\RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository $source = null)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get source
     *
     * @return RRaven\Bundle\LaughingbearBundle\Entity\GithubRepository 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set organization
     *
     * @param RRaven\Bundle\LaughingbearBundle\Entity\GithubOrganization $organization
     * @return GithubRepository
     */
    public function setOrganization(\RRaven\Bundle\LaughingbearBundle\Entity\GithubOrganization $organization = null)
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * Get organization
     *
     * @return RRaven\Bundle\LaughingbearBundle\Entity\GithubOrganization 
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set github_id
     *
     * @param integer $githubId
     * @return GithubRepository
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
}
<?php

namespace RRaven\Bundle\LaughingbearBundle\Annotations\Api;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Link extends Annotation {
	
	/**
	 * eg: "owner/url"
	 * @var String data-key containing the URL to fetch from
	 */
	public $url = null;
	
	/**
	 * eg: "owner/login"
	 * @var String data-key containing the uniquely identifiable field
	 */
	public $id = null;
	
	/**
	 * eg: "login"
	 * @var String the field on the link-ee to check the ID against
	 */
	public $target = null;
	
	/**
	 * eg: "true"
	 * @var boolean whether to fail if missing. Implies auto_import.
	 */
	public $required = false;
	
	/**
	 * eg: "true"
	 * @var boolean should always import link, even if not the import-target?
	 */
	public $auto_import = false;
    
    /**
     * eg: "User"
     * or: "Namespace\Bundle\Entity\User"
     * @var String the entity type to link to
     */
    public $targetEntity = null;
}
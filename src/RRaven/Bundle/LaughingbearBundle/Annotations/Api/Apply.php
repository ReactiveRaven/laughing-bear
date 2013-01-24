<?php

namespace RRaven\Bundle\LaughingbearBundle\Annotations\Api;

use Doctrine\Common\Annotations\Annotation;

/**
 * Used to signify that an API key should be applied to manufacture a child 
 * object directly.
 * 
 * @Annotation
 */
class Apply extends Annotation {
	/**
	 * Key to extract from the data
	 * @var string
	 */
	public $key = null;
	
	/**
	 * Targets to assign to the entity's keys.
	 * Targets the Apply-ee with the keys, and pulls values from the current 
	 * element based on the values.
	 * 
	 * eg: {user: "user", repo: "_SELF"}
	 * - set 'user' on the target to be the entity behind our 'user' key, 
	 * - use the current entity as the 'repo' entity on the target.
	 * 
	 * @var string[]
	 */
	public $targets = array();
    
    /**
     * eg: "Attachment"
     * or: "Namespace\Bundle\Entity\Attachment"
     * @var String the entity type to apply the data to
     */
    public $targetEntity = null;
}
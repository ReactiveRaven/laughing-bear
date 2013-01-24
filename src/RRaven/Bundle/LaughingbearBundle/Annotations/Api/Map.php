<?php

namespace RRaven\Bundle\LaughingbearBundle\Annotations\Api;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Map extends Annotation {
	/**
	 *
	 * @var string[] searches for given keys to use as the property's value
	 */
	public $keys = array();
}
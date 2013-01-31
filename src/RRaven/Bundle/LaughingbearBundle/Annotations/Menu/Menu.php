<?php

namespace RRaven\Bundle\LaughingbearBundle\Annotations\Menu;

use Doctrine\Common\Annotations\Annotation;

/**
 * Used to signify that an API key should be applied to manufacture a child 
 * object directly.
 * 
 * @Annotation
 */
class Menu extends Annotation {
	/**
   * The name to display on the menu
   *
   * @var string
   */
	public $name = null;
  
  /**
   * 
   * 
   * @var string
   */
  public $parent = null;
  
  /**
   * If set, the value of 'dropdown' is used as the parent of this menu item.
   *
   * @var string
   */
  public $dropdown = null;
}
<?php

namespace RRaven\Bundle\LaughingbearBundle\Annotations\Menu;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Menu extends Annotation
{
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
    public $path = null;

    /**
     * Attempts to place the annotated item before the given route.
     *
     * @var string 
     */
    public $before = null;
    
    private $_isRoot = false;
    
    private $_dirty = false;
    
    public function getIsRoot() 
    {
        return !!$this->_isRoot;
    }
    
    public function setIsRoot($isRoot) {
        $this->_isRoot = !!$isRoot;
    }
    
    public function getName()
    {
        return $this->name ? $this->name : $this->value;
    }

    public function getPath()
    {
        if (!is_null($this->path) && !is_array($this->path)) {
            $this->path = array($this->path);
        }
        
        return $this->path;
    }
    
    private $_nodes = array();
    
    private $_known_routes = array();
    
    protected function addChildren($children) {
        $childPath = $this->getPathForChildMenu();
        foreach ($children as $child) {
            if ($child instanceof Menu) {
                $path = array_slice($child->getPath(), count($childPath));
                $this->addMenu($child, $path);
            } else if ($child instanceof Item) {
                $path = array_slice($child->getPath(), count($childPath));
                $this->addItem($child, $path);
            } else {
                throw new \InvalidArgumentException("Unrecognised child type");
            }
        }
    }
    
    public function sort() {
        if ($this->_dirty) {
            $unsorted = $this->_nodes;
            $sorted = array();
            foreach ($unsorted as $key => $val) {
                if (!$val->before || !in_array($val->before, $this->_known_routes)) {
                    $sorted[$key] = $val;
                    unset($unsorted[$key]);
                }
            }
            $somethingChanged = true;
            while (count($unsorted) && $somethingChanged) {
                $somethingChanged = false;
                foreach ($unsorted as $key => $val) {
                    $before = $val->before;
                    $newSorted = array();
                    foreach ($sorted as $skey => $sval) {
                        if ($skey === $before || ($sval instanceof Item && $sval->getRoute() === $before)) {
                            $newSorted[$key] = $val;
                        }
                        $newSorted[$skey] = $sval;
                    }
                    $sorted = $newSorted;
                }
            }
            
            foreach ($sorted as $sval) {
                if ($sval instanceof Menu) {
                    $sval->sort();
                }
            }
            $this->_nodes = $sorted;
        }
        
        $this->_dirty = false;
    }
    
    private function getPathForChildMenu() {
        if ($this->_isRoot) {
            return array();
        } else {
            $existing_path = $this->getPath();
            if (is_null($existing_path)) {
                $existing_path = array();
            }
            return array_merge($existing_path, array($this->getName()));
        }
    }
    
    public function addItem(Item $item, $nodepath = null) {
        // Nodepath to add item to (eg: `['animal', 'dog', 'pug']`)
        $nodepath = is_array($nodepath) ? $nodepath : $item->getPath();
        
        // Are we at the deepest point of the nodepath?
        if (count($nodepath))
        { // No, there are more nodes to go..
            
            // We're descenting, so only interested in our own level. Pull off the
            // first node from the nodepath.
            $nodeNow = array_shift($nodepath);
            
            // Is there a node already existing?
            if (!isset($this->_nodes[$nodeNow])) 
            { // No, so create a menu..
                $newMenu = new Menu(array());
                $newMenu->path = $this->getPathForChildMenu();
                $newMenu->name = $nodeNow;
                $newMenu->addItem($item, $nodepath);
                $this->addMenu($newMenu, array($newMenu->getName()));
            } 
            else 
            { // Yes, so add to it..
                
                $existing = $this->_nodes[$nodeNow];
                // Is it an item?
                if ($existing instanceof Item) 
                { // Yes, so move it into a menu first..
                    $newMenu = new Menu(array());
                    $newMenu->path = $this->getPathForChildMenu();
                    $newMenu->name = $nodeNow;
                    $newMenu->addItem($existing);
                    $this->addMenu($newMenu, array($newMenu->getName()));
                }
                // Add the item to the sub-menu
                $this->_nodes[$nodeNow]->addItem($item, $nodepath);
            }
        } 
        else 
        { // Yes, no more nodes to go..
            
            // Does this node already exist?
            if (isset($this->_nodes[$item->getName()])) 
            { // Yes, so figure out how to merge this item in..
                
                $existing = $this->_nodes[$item->getName()];
                // Is the existing a menu?
                if ($existing instanceof Menu) 
                { // Yes, so just add it to the menu.
                    $existing->addItem($item);
                } 
                else 
                { // No, so just overwrite the old item.
                    $this->_nodes[$item->getName()] = $item;
                }
            } 
            else 
            { // No, so set it now!
                $this->_nodes[$item->getName()] = $item;
            }
            
            $this->_known_routes[] = $item->getName();
            $this->_known_routes[] = $item->getRoute();
        }
        
        // Just edited the array. Flag it up to need sorting.
        $this->_dirty = true;
    }
    
    public function addMenu(Menu $menu, $nodepath = null) {
        // Nodepath to add item to (eg: `['animal', 'dog', 'pug']`)
        $nodepath = is_array($nodepath) ? $nodepath : $menu->getPath();
        
        // Are we at the deepest point of the nodepath?
        if (count($nodepath))
        { // No, there are more nodes to go..
            
            // We're descenting, so only interested in our own level. Pull off the
            // first node from the nodepath.
            $nodeNow = array_shift($nodepath);
            
            // Is there a node already existing?
            if (!isset($this->_nodes[$nodeNow])) 
            { // No, so create a menu..
                $newMenu = new Menu(array());
                $newMenu->name = $nodeNow;
                $newMenu->path = $this->getPathForChildMenu();
                $newMenu->addMenu($menu, $nodepath);
                $this->addMenu($newMenu, array());
            } 
            else 
            { // Yes, so add to it..
                
                $existing = $this->_nodes[$nodeNow];
                // Is it an item?
                if ($existing instanceof Item) 
                { // Yes, so move it into a menu first..
                    $newMenu = new Menu(array());
                    $newMenu->name = $nodeNow;
                    $newMenu->path = $this->getPathForChildMenu();
                    $existingPath = array_slice($existing->getPath(), count($this->getPathForChildMenu()));
                    var_dump($existingPath);
                    $newMenu->addItem($existing, $existingPath);
                    $this->addMenu($newMenu, array());
                }
                // Add the menu to the sub-menu
                $this->_nodes[$nodeNow]->addMenu($menu, $nodepath);
            }
        } 
        else 
        { // Yes, no more nodes to go..
            
            // Does this node already exist?
            if (isset($this->_nodes[$menu->getName()])) 
            { // Yes, so figure out how to merge this item in..
                
                $existing = $this->_nodes[$menu->getName()];
                // Is the existing a menu?
                if ($existing instanceof Menu) 
                { // Yes, so merge existing children into ours.
                    
                    // Take a copy of children, so new are always the freshest 
                    // if a child of the same name already exists.
                    $menuChildren = $menu->getChildren();
                    $menu->addChildren($existing->getChildren());
                    $menu->addChildren($menuChildren);
                    
                } 
                else 
                { // No, so gobble up the old item.
                    $existingPath = array_slice($existing->getPath(), count($this->getPathForChildMenu()));
                    var_dump($existingPath, $existing);
                    $menu->addItem($existing, $existingPath);
                }
                $this->_nodes[$menu->getName()] = $menu;
            }
            else 
            { // No, so set it now!
                $this->_nodes[$menu->getName()] = $menu;
            }
            
            $this->_known_routes[] = $menu->getName();
        }
        
        // Just edited the array. Flag it up to need sorting.
        $this->_dirty = true;
    }
    
    protected function getChildren() {
        return $this->_nodes;
    }
}
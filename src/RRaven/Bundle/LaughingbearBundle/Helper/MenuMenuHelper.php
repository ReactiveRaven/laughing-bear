<?php

namespace RRaven\Bundle\LaughingbearBundle\Helper;

use RRaven\Bundle\LaughingbearBundle\Annotations\Menu\Menu as MenuMenu;
use RRaven\Bundle\LaughingbearBundle\Annotations\Menu\Item as MenuItem;
use JMS\SecurityExtraBundle\Annotation\Secure;

class MenuMenuHelper
{

    private $_annotation_reader = null;
    private $_logger = null;
    private $_container = null;

    public function __construct($annotation_reader, $logger, $container)
    {
        $this->_annotation_reader = $annotation_reader;
        $this->_logger = $logger;
        $this->_container = $container;
    }

    /**
     * @param \Doctrine\Common\Annotations\FileCacheReader $annotation_reader
     */
    public function setAnnotationReader($annotation_reader)
    {
        $this->_annotation_reader = $annotation_reader;
    }

    private function getAnnotationReader()
    {
        return $this->_annotation_reader;
    }

    public function setLogger($logger)
    {
        $this->_logger = $logger;
    }

    /**
     * @return \Monolog\Logger
     */
    private function getLogger()
    {
        return $this->_logger;
    }

    public function sniffMenus()
    {

        $menu_items = array();
        $menu_menus = array();

        $router = $this->_container->get("router");
        /* @var $router \Symfony\Bundle\FrameworkBundle\Routing\Router */
        $routes = $router->getRouteCollection()->all();

        $annotation_reader = $this->getAnnotationReader();

        foreach ($routes as $route_name => $route) {
            /* @var $route \Symfony\Component\Routing\Route */
            $controller = $route->getDefault("_controller");
            if (strpos($controller, "::") !== false) {
                $bits = explode("::", $controller);
                $class = $bits[0];
                $method = $bits[1];

                $reflectionClass = new \ReflectionClass($class);
                $reflectionMethod = $reflectionClass->getMethod($method);

                $methodAnnotations = $annotation_reader->getMethodAnnotations($reflectionMethod);

                foreach ($methodAnnotations as $annotation) {
                    if ($annotation instanceof MenuItem) {
                        $annotation->setRoute("@" . $route_name);
                        $menu_items[] = $annotation;
                    } else if ($annotation instanceof MenuMenu) {
                        $menu_menus[] = $annotation;
                    }
                }
            }
        }
        
        $menu = new MenuMenu(array());
        $menu->setIsRoot(true);
        
        foreach ($menu_items as $item /* @var $item MenuItem */) {
            $menu->addItem($item);
        }
        
        foreach ($menu_menus as $submenu /* @var $menu MenuMenu */) {
            $menu->addMenu($submenu);
        }
        
        var_dump($menu);die();
        
        $menu->sort();
        
        var_dump($menu);die();
    }

}

?>

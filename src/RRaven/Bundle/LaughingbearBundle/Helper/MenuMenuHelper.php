<?php

namespace RRaven\Bundle\LaughingbearBundle\Helper;

use RRaven\Bundle\LaughingbearBundle\Annotations\Menu\Menu as MenuMenu;

class MenuMenuHelper {
	
	private $_annotation_reader = null;
	private $_logger = null;
  private $_container = null;
	
	public function __construct($annotation_reader, $logger, $container) {
		$this->_annotation_reader = $annotation_reader;
		$this->_logger = $logger;
    $this->_container = $container;
	}
	
	/**
	 * @param \Doctrine\Common\Annotations\FileCacheReader $annotation_reader
	 */
	public function setAnnotationReader($annotation_reader) {
		$this->_annotation_reader = $annotation_reader;
	}
	
	private function getAnnotationReader()
	{
		return $this->_annotation_reader;
	}
	
	public function setLogger($logger) {
		$this->_logger = $logger;
	}
	
	/**
	 * @return \Monolog\Logger
	 */
	private function getLogger() {
		return $this->_logger;
	}
  
  public function sniffMenus() {
    
    $router = $this->_container->get("router");
    /* @var $router \Symfony\Bundle\FrameworkBundle\Routing\Router */
    $routes = $router->getRouteCollection()->all();
    
    $annotation_reader = $this->getAnnotationReader();
    
    foreach ($routes as $route) {
      /* @var $route \Symfony\Component\Routing\Route */
      var_dump($route);
      $controller = $route->getDefault("_controller");
      if (strpos($controller, "::") !== false) {
        $bits = explode("::", $controller);
        $class = $bits[0];
        $method = $bits[1];
        
        $reflectionClass = new \ReflectionClass($class);
        $reflectionMethod = $reflectionClass->getMethod($method);
        
        $apiMapAnnotation = $annotation_reader->getMethodAnnotation($reflectionMethod, "RRaven\\Bundle\\LaughingbearBundle\\Annotations\\Menu\\Menu");
        
        var_dump($apiMapAnnotation);
      }
    }
    die();
    foreach ($dump as $item) {
      var_dump($item);die();
    }
    
    var_dump($dump);
    die();
  }
	
	public function applyDataToEntity($data, $entity) {
		
		$logger = $this->getLogger();
		$logger->debug("Started applying data to entity", array("entity" => $entity, "data" => $data));
		
		$annotation_reader = $this->getAnnotationReader();
		
		$reflectionClass = new \ReflectionClass($entity);
		
		foreach (
			$reflectionClass->getProperties()
			as $reflectionProperty /* @var $reflectionProperty \ReflectionProperty */
	  ) {
			$apiMapAnnotation = $annotation_reader->getPropertyAnnotation($reflectionProperty, "RRaven\\Bundle\\LaughingbearBundle\\Annotations\\Api\\Map");
			
			if ($apiMapAnnotation instanceof ApiMap)
			{
				$logger->debug("--ApiMap annotation found");
				foreach ($apiMapAnnotation->keys as $key)
				{
					$logger->debug("----Found key '" . $key ."'");
					if (array_key_exists($key, $data) && !is_array($data[$key]))
					{
						$logger->debug("------Found matching key in data array", array("key" => $key, "value" =>$data[$key]));
						// CamelCase any underscore_delimited properties to get the setter
						$setter = "set" . implode("", explode(" ", ucwords(str_replace("_", " ", $reflectionProperty->getName()))));

						$logger->debug("------Setting with setter '" . $setter . "'");

						// Make sure the method is accessible
						if (
							$reflectionClass->hasMethod($setter) 
							&& $reflectionClass->getMethod($setter)->isPublic()
						)
						{
							$value = $data[$key];
							
							$columnAnnotation = $annotation_reader->getPropertyAnnotation($reflectionProperty, "Doctrine\\ORM\\Mapping\\Column");
							if ($columnAnnotation instanceof \Doctrine\ORM\Mapping\Column) {
								switch (strtolower($columnAnnotation->type)) {
									case "datetime":
										$value = new \DateTime($value);
								}
							}
							
							$entity->$setter($value);
						}
					}
				}
			}
		}
		
		return $this;
	}
}

?>

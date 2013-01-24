<?php

namespace RRaven\Bundle\LaughingbearBundle\Helper;

use RRaven\Bundle\LaughingbearBundle\Annotations\Api\Map as ApiMap;

class ApiMapHelper {
	
	private $_annotation_reader = null;
	private $_logger = null;
	
	public function __construct($annotation_reader, $logger) {
		$this->_annotation_reader = $annotation_reader;
		$this->_logger = $logger;
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

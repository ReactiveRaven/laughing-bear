<?php

namespace RRaven\Bundle\LaughingbearBundle\Helper;

use RRaven\Bundle\LaughingbearBundle\Annotations\Api;

class ApiHelper
{

    private $annotation_reader = null;
    private $logger = null;
    private $buzz_browser = null;

    /**
     * @param \Doctrine\Common\Annotations\AnnotationReader $annotation_reader
     * @param \Monolog\Logger $logger
     * @param \Buzz\Browser $buzz_browser
     */
    public function __construct($annotation_reader, $logger, $buzz_browser)
    {
        $this->annotation_reader = $annotation_reader;
        $this->logger = $logger;
        $this->buzz_browser = $buzz_browser;
    }

    /**
     * @param \Buzz\Browser $buzz_browser
     */
    public function setBuzzBrowser($buzz_browser)
    {
        $this->buzz_browser = $buzz_browser;
    }

    /**
     * @return \Buzz\Browser
     */
    private function getBuzzBrowser()
    {
        return $this->buzz_browser;
    }

    /**
     * @param \Doctrine\Common\Annotations\FileCacheReader $annotation_reader
     */
    public function setAnnotationReader($annotation_reader)
    {
        $this->annotation_reader = $annotation_reader;
    }

    private function getAnnotationReader()
    {
        return $this->annotation_reader;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return \Monolog\Logger
     */
    private function getLogger()
    {
        return $this->logger;
    }

    /**
     * Applies data to an object, based on the annotations in the object.
     * 
     * @param mixed[] $data array to pull data from
     * @param object $entity to apply data to
     * @return object[] additional entities generated further down the chain
     */
    public function apply($data, $entity)
    {
        try {
            $additionalEntities =
                // to remove empty sub-arrays and false
                array_filter(
                    array_merge(
                        $this->applyLinks($data, $entity),
                        $this->applyMaps($data, $entity),
                        $this->applyApplies($data, $entity)
                    )
                );
        } catch (Exception $exception) {
            $this
                ->getLogger()
                ->addError(
                    "Cannot import data into entity in " . __CLASS__ . "::" . __FUNCTION__ . "()",
                    array(
                        "data" => $data,
                        "entity" => $entity,
                        "exception" => $exception
                    )
                );
            
        }
        
        return $additionalEntities;
    }
    
    /**
     * Searches an entity for properties annotated with the given annotation 
     * class.
     * Expects class names with backslashes, which will probably need escaping.
     * Returns an array in the form [{annotation: ... , property: ... }]
     * 
     * @param object $entity to reflect
     * @param string $annotation_class_name to search for
     * @return array of 'annotation,property' tuples
     */
    private function findAnnotatedProperties($entity, $annotation_class_name)
    {
        $results = array();
        
        $annotation_reader = $this->getAnnotationReader();

        $reflectionClass = new \ReflectionClass($entity);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            /* @var $reflectionProperty \ReflectionProperty */
            $apiLinkAnnotation = $annotation_reader->getPropertyAnnotation(
                $reflectionProperty,
                $annotation_class_name
            );

            if (get_class($apiLinkAnnotation) == $annotation_class_name) {
                $results[] =
                    array(
                        "annotation" => $apiLinkAnnotation,
                        "property" => $reflectionProperty,
                        "class" => $reflectionClass
                    );
            }
        }
        
        return $results;
    }

    private function applyLinks($data, $entity)
    {
        /*
          public $url = null;
          public $id = null;
          public $references = null;
          public $required = false;
          public $targetEntity = null;
         */
        $logger = $this->getLogger();
        $logger->debug(
            "Started " . __CLASS__ . "::" . __FUNCTION__,
            array("entity" => $entity, "data" => $data)
        );
        
        $properties =
            $this->findAnnotatedProperties(
                $entity,
                "RRaven\\Bundle\\LaughingbearBundle\\Annotations\\Api\\Link"
            );
        
        if (count($properties)) {
            throw new Exception("Not yet implemented!");
        }

        return $this;
    }

    private function applyApplies($data, $entity)
    {
        $logger = $this->getLogger();
        $logger->debug(
            "Started " . __CLASS__ . "::" . __FUNCTION__,
            array("entity" => $entity, "data" => $data)
        );
        
        $results =
            $this->findAnnotatedProperties(
                $entity,
                "RRaven\\Bundle\\LaughingbearBundle\\Annotations\\Api\\Apply"
            );
        
        $additionalEntities = array();
        
        if (count($results)) {
            foreach ($results as $result) {
                $annotation = $result["annotation"];
                /* @var $annotation Api\Apply */
                $reflectionProperty = $result["property"];
                /* @var $reflectionProperty \ReflectionProperty */
                $reflectionClass = $result["class"];
                /* @var $reflectionClass \ReflectionClass */
                
                $path = ($annotation->key ? $annotation->key : $annotation->value);
                if (!$path) {
                    throw new Exception("Cannot find key or value on Api\Apply");
                }
                
                
                $setter = "set" . ucfirst($this->camelcase($reflectionProperty->getName()));
                if (!$reflectionClass->hasMethod($setter) || !$reflectionClass->getMethod($setter)->isPublic()) {
                    throw new Exception("Cannot find setter '" . $setter . "'");
                }
                
                if (($drilldownData = $this->drilldown($data, $path))) {
                    $targetEntity = new $annotation->targetEntity();
                    $newEntities = $this->apply($drilldownData, $targetEntity);
                    $entity->$setter($targetEntity);
                    
                    // We want the previous entities, this new entity, and any
                    // entities generated further down the chain all in one
                    // array
                    $additionalEntities =
                        array_merge(
                            $additionalEntities,
                            array($targetEntity),
                            $newEntities
                        );
                }
            }
        }

        return $additionalEntities;
    }
    
    private function drilldown($data, $path)
    {
        // TODO: figure out how to do this with escaped characters, eg:
        // "fish\/chips/peas" => ["fish/chips", "peas"]
        if (is_string($path)) {
            $path = explode("/", $path);
        }
        
        $result=$data;
        foreach ($path as $key) {
            if (is_array($result) && isset($result[$key])) {
                $result= $result[$key];
            } else {
                $result = false;
            }
        }
    }
    
    /**
     * Converts underscored strings to camelcased strings.
     * 
     * eg: fish_and_chips -> fishAndChips
     * 
     * Note; not doing anything clever: FISH_aND_cHIPS -> fISHANDCHIPS
     * 
     * @param String $string to camelcase
     * @return String camelcased
     */
    private function camelcase($string)
    {
        return
            // "FishChips" -> "fishChips"
            lcfirst(
                // "Fish Chips" -> "FishChips"
                str_replace(
                    " ",
                    "",
                    // "fish chips" -> "Fish Chips"
                    ucwords(
                        // "fish_chips" -> "fish chips"
                        str_replace(
                            "_",
                            " ",
                            $string
                        )
                    )
                )
            );
    }

    private function applyMaps($data, $entity)
    {

        $logger = $this->getLogger();
        $logger->debug("Started applying data to entity", array("entity" => $entity, "data" => $data));
        
        $annotation_reader = $this->getAnnotationReader();

        $properties = $this->findAnnotatedProperties($entity, "RRaven\\Bundle\\LaughingbearBundle\\Annotations\\Api\\Map");
        
        foreach ($properties as $array) {
            $apiMapAnnotation = $array["annotation"];
            $reflectionProperty = $array["property"];
            $reflectionClass = $array["class"];
            
            foreach ($apiMapAnnotation->keys as $key) {
                if (array_key_exists($key, $data) && !is_array($data[$key])) {
                    // CamelCase any underscore_delimited properties to get the setter
                    $setter = "set" . ucfirst($this->camelcase($reflectionProperty->getName()));

                    // Make sure the method is accessible
                    if (
                        $reflectionClass->hasMethod($setter)
                        && $reflectionClass->getMethod($setter)->isPublic()
                    ) {
                        $value = $data[$key];

                        $columnAnnotation =
                            $annotation_reader->getPropertyAnnotation(
                                $reflectionProperty,
                                "Doctrine\\ORM\\Mapping\\Column"
                            );

                        if ($columnAnnotation instanceof \Doctrine\ORM\Mapping\Column) {
                            switch (strtolower($columnAnnotation->type)) {
                                case "datetime":
                                    $value = new \DateTime($value);
                            }
                        }

                        $entity->$setter($value);
                    } else {
                        $logger->debug("Cannot find setter '" . $setter . "' or it is not public");
                    }
                }
            }
        }

        return $this;
    }
}

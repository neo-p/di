<?php

namespace NeoP\DI;

use NeoP\Annotation\AnnotationProvider;
use NeoP\DI\DependType;
use NeoP\Log\Log;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private static $definitionSources = [];
    private static $definitions = [];
    private static $handlers = [];
    private static $refletcionAnnotation = [];

    public static function init()
    {
        self::$handlers = AnnotationProvider::getHandlers();
        self::setDefinition();
    }

    public static function setDefinition()
    {
        $annotationClasses = AnnotationProvider::getAnnotations();
        foreach ($annotationClasses as $annotationClass) {
            $className = $annotationClass->getClass();
            if( $className ) {
                self::getClassObject($className);
            }
        }
    }

    public static function getDefinition(string $className)
    {
        return self::$definitions[$className];
    }


    public static function hasDefinition(string $className)
    {
        return isset(self::$definitions[$className]);
    }

    
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get(string $className)
    {
        return self::$definitions[$className];
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $className)
    {
        return isset(self::$definitions[$className]);
    }

    public static function getClassObject(string $className, int $type = DependType::SINGLETON)
    {
        if (isset(self::$definitions[$className]) && $type = DependType::SINGLETON) {
            return self::$definitions[$className];
        }
        $annotationClass = AnnotationProvider::getAnnotations()[$className];
        
        // 处理类注解
        foreach ($annotationClass->getAnnotations() as $annotation) {
            $annotationMapping = get_class($annotation);
            if (! isset(self::$handlers[$annotationMapping])) {
                Log::stdout("not extsis mapping {$annotationMapping} for " . $className, 0, Log::MODE_DEFAULT, Log::FG_YELLOW);
            } else {
                $handler = new self::$handlers[$annotationMapping]($className);
                $reflectionClass = $annotationClass->getReflectionClass();
                $handler->handle($annotation, $reflectionClass);
            }
            if (! isset(self::$refletcionAnnotation[$annotationMapping][$className])) {
                self::$refletcionAnnotation[$annotationMapping][$className] = 1;
            }
        }
        self::$definitions[$className] = $annotationClass->getReflectionClass()->newInstance();

        foreach ($annotationClass->getProperties() as $property) {
            if ( $property ) {
                foreach ($property->getAnnotations() as $annotation) {
                    $annotationMapping = get_class($annotation);
                    if(! isset(self::$handlers[$annotationMapping])) {
                        Log::stdout("not extsis mapping {$annotationMapping}", 0, Log::MODE_DEFAULT, Log::FG_YELLOW);
                    } else {
                        $handler = new self::$handlers[$annotationMapping]($className);
                        $handler->handle($annotation, $property);
                    }
                }
            }
        }

        foreach ($annotationClass->getMethods() as $method) {
            if ( $method ) {
                foreach ($method->getAnnotations() as $annotation) {
                    $annotationMapping = get_class($annotation);
                    if (! isset(self::$handlers[$annotationMapping])) {
                        Log::stdout("not extsis mapping {$annotationMapping}", 0, Log::MODE_DEFAULT, Log::FG_YELLOW);
                    } else {
                        $handler = new self::$handlers[$annotationMapping]($className);
                        $handler->handle($annotation, $method->getReflectionMethod());
                    }
                }
            }
        }
        
        return self::$definitions[$className];
    }

}
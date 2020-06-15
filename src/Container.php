<?php

namespace NeoP\DI;

use NeoP\Annotation\AnnotationProvider;
use NeoP\DI\DependType;
use NeoP\Log\Log;

class Container
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
                $handler->handle($annotation, $annotationClass->getReflectionClass());
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
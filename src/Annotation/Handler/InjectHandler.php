<?php

namespace NeoP\DI\Annotation\Handler;

use NeoP\DI\Container;

use NeoP\Annotation\Annotation\Handler\Handler;
use NeoP\Annotation\Annotation\Mapping\AnnotationHandler;
use NeoP\Annotation\Entity\AnnotationProperty;

use NeoP\DI\Annotation\Mapping\Inject;
use NeoP\DI\InjectType;

use PhpDocReader\PhpDocReader;
use ReflectionProperty;

/**
 * @AnnotationHandler(Inject::class)
 */
class InjectHandler extends Handler
{
    public function handle(Inject $annotation, AnnotationProperty &$property)
    {
        $reader = new PhpDocReader();

        $injectClass = $reader->getPropertyClass($property->getReflectionProperty());
        $injectObject = Container::getClassObject($injectClass, InjectType::getInjectType($injectClass));
        $property->getReflectionProperty()->setAccessible(true);
        $property->getReflectionProperty()->setValue(Container::getDefinition($this->className), $injectObject);
        unset($property);
    }
}
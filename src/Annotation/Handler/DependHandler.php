<?php

namespace NeoP\DI\Annotation\Handler;

use NeoP\Annotation\Annotation\Handler\Handler;
use NeoP\Annotation\Annotation\Mapping\AnnotationHandler;

use NeoP\DI\Annotation\Mapping\Depend;
use NeoP\DI\InjectType;

use ReflectionClass;

/**
 * @AnnotationHandler(Depend::class)
 */
class DependHandler extends Handler
{
    public function handle(Depend $annotation, ReflectionClass $reflectionClass)
    {
        if ($annotation->getName() != NULL) {
            InjectType::setInjectType($annotation->getName(), $annotation->getType());
        } else {
            InjectType::setInjectType($this->className, $annotation->getType());
        }
    }
}
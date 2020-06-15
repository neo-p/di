<?php

namespace NeoP\DI\Annotation\Mapping;

use NeoP\Annotation\Annotation\Mapping\AnnotationMappingInterface;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;
use NeoP\DI\DependType;

use function annotationBind;

/** 
 * Class Inject
 * 
 * @Annotation
 * @Target("PROPERTY")
 * @Attributes({
 *     @Attribute("name", type="string"),
 * })
 */
final class Inject implements AnnotationMappingInterface
{
    private $name = "";

    private $type = DependType::SINGLETON;

    function __construct($params)
    {
        annotationBind($this, $params, 'setName');
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setType(int $type = DependType::SINGLETON): void
    {
        $this->type = $type;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
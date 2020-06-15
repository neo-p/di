<?php

namespace NeoP\DI;

use NeoP\DI\DependType;
use NeoP\DI\Exception\DIException;

class InjectType
{
    private static $injectType = [];

    public static function setInjectType(string $class, string $type): void
    {
        if (! in_array($type, [DependType::SINGLETON, DependType::FACTORY, DependType::CONNECT])) {
            throw new DIException("Not exsits this Depend type!");
        }
        self::$injectType[$class] = $type;
    }

    public static function getInjectType(string $class): string
    {
        return self::$injectType[$class] ?? DependType::SINGLETON;
    }

    public static function getInjectTypes(): array
    {
        return self::$injectType;
    }

    public static function hasInjectType(string $class): bool
    {
        return isset(self::$injectType[$class]);
    }
}

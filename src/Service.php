<?php

namespace Xofttion\IoC;

use Xofttion\IoC\Contracts\IFactory;

class Service
{
    private IFactory $factory;

    private function __construct(IFactory $factory)
    {
        $this->factory = $factory;
    }

    public static function build(string $classFactory): self
    {
        return new static(new $classFactory());
    }

    public function deploy(string $ref)
    {
        $object = $this->factory->build($ref);

        if (is_null($object)) {
            return null;
        }

        return $object->value();
    }
}

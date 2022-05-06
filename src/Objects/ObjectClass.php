<?php

namespace Xofttion\IoC\Objects;

use Xofttion\IoC\Contracts\IObject;

class ObjectClass implements IObject
{
    private string $class;

    private function __construct(string $class)
    {
        $this->class = $class;
    }

    public static function build(string $class): self
    {
        return new static($class);
    }

    public function value()
    {
        return new $this->class();
    }
}

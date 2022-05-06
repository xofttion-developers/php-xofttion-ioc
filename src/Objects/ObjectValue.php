<?php

namespace Xofttion\IoC\Objects;

use Xofttion\IoC\Contracts\IObject;

class ObjectValue implements IObject
{
    private $value;

    private function __construct($value)
    {
        $this->value = $value;
    }

    public static function build($value): self
    {
        return new static($value);
    }

    public function value()
    {
        return $this->value;
    }
}

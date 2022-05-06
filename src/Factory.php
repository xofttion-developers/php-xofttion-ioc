<?php

namespace Xofttion\IoC;

use Xofttion\IoC\Contracts\IFactory;
use Xofttion\IoC\Contracts\IObject;
use Xofttion\IoC\Objects\ObjectValue;

class Factory implements IFactory
{
    public function build(string $ref): ?IObject
    {
        return ObjectValue::build($ref);
    }
}

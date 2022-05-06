<?php

namespace Xofttion\IoC\Contracts;

interface IFactory
{
    public function build(string $ref): ?IObject;
}

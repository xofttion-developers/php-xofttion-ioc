<?php

namespace Xofttion\IoC;

use ReflectionClass;
use ReflectionMethod;
use Xofttion\Kernel\Str;

class ClassBuilder
{

    // Métodos de la clase ClassBuilder

    /**
     * 
     * @param ClassInstance $classInstance
     * @param ContextContainer $context
     * @param string $classFactory
     * @return mixed
     */
    public function create(ClassInstance $classInstance, ContextContainer $context, string $classFactory)
    {
        $classValue = $this->build($classInstance->getClass());
        $reflection = new ReflectionClass($classValue);

        $dependencies = $classInstance->getDependencies();

        foreach ($dependencies->values() as $classDependency => $dependency) {
            $methodSetter = $this->getMethodName($classDependency);
            $injector = $this->getInjector($reflection, $methodSetter);

            if (is_defined($injector)) {
                if ($dependency->isShared()) {
                    $sharedValue = $context->getShared($classDependency);
        
                    if (is_null($sharedValue)) {
                        $sharedValue = $context->create($classFactory, $dependency->getClass());
                        $context->attachShared($classDependency, $sharedValue);
                    }
        
                    $dependencyValue = $sharedValue;
                }
                else {
                    $dependencyValue = $context->create($classFactory, $dependency->getClass());
                }
                
                $injector->invoke($classValue, $dependencyValue);
            }
        }

        return $classValue;
    }

    /**
     * 
     * @param ReflectionClass $reflection
     * @param string $methodSetter
     * @return ReflectionMethod|null
     */
    private function getInjector(ReflectionClass $reflection, string $methodSetter): ?ReflectionMethod
    {
        if (!$reflection->hasMethod($methodSetter)) {
            return null;
        }

        $injector = $reflection->getMethod($methodSetter); // Método

        return (!$injector->isPublic()) ? null : $injector;
    }

    /**
     * 
     * @param string $class
     * @return mixed
     */
    protected function build(string $class)
    {
        return new $class();
    }

    /**
     * 
     * @param string $propertyName
     * @return string
     */
    protected function getMethodName(string $propertyName): string
    {
        return Str::getCamelCase()->ofSnakeSetter($propertyName);
    }
}

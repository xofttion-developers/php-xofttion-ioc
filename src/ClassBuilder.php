<?php

namespace Xofttion\IoC;

use ReflectionClass;
use ReflectionMethod;

use Xofttion\Kernel\Str;

class ClassBuilder {
    
    // Métodos de la clase ClassBuilder
    
    /**
     * 
     * @param ClassInstance $classInstance
     * @param ContextContainer $context
     * @param string $classFactory
     * @return object
     */
    public function create(ClassInstance $classInstance, ContextContainer $context, string $classFactory) {
        $instance   = $this->build($classInstance->getClass()); // Instancia
        $reflection = new ReflectionClass($instance);
        
        foreach ($classInstance->getDependences()->values() as $propertyName => $dependency) {
            $methodSetter = $this->getMethodName($propertyName); // Nombre del Método
            $injector     = $this->getDependencyInjector($reflection, $methodSetter);

            if (!is_null($injector)) {
                $injector->invoke($instance, $this->createDependency($dependency, $context, $classFactory, $propertyName));
            } // Inyectando dependencia en clase en construcción
        }
        
        return $instance; // Retornando instancia de la clase
    }
    
    /**
     * 
     * @param ReflectionClass $reflection
     * @param string $methodSetter
     * @return ReflectionMethod|null
     */
    private function getDependencyInjector(ReflectionClass $reflection, string $methodSetter): ?ReflectionMethod {
        if (!$reflection->hasMethod($methodSetter)) {
            return null; // Clase no contiene método para inyectar dependencia
        }
        
        $injector = $reflection->getMethod($methodSetter); // Método de la propiedad

        return (!$injector->isPublic()) ? null : $injector; // Retornando método
    }
    
    /**
     * 
     * @param Dependency $dependency
     * @param ContextContainer $context
     * @param string $classFactory
     * @param string $classDependency
     * @return object
     */
    private function createDependency(Dependency $dependency, ContextContainer $context, string $classFactory, string $classDependency) {
        if ($dependency->isShared()) {
            $instance = $context->getSharedInstance($classDependency); // Consultando
            
            if (is_null($instance)) {
                $instance = $context->create($classFactory, $dependency->getClass());
                $context->setSharedInstance($classDependency, $instance);
            }
            
            return $instance; // Retornando instancia compartida
        } else {
            return $context->create($classFactory, $dependency->getClass()); // Nueva instancia
        }
    }

    /**
     * 
     * @param string $class
     * @return object
     */
    protected function build(string $class) {
        return new $class(); // Instanciando clase
    }

    /**
     * 
     * @param string $propertyName
     * @return string
     */
    protected function getMethodName(string $propertyName): string {
        return Str::getCamelCase()->ofSnakeSetter($propertyName);
    }
}
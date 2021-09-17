<?php

namespace Xofttion\IoC;

use Xofttion\Kernel\Contracts\IJson;
use Xofttion\Kernel\Structs\Json;
use Xofttion\IoC\Contracts\IDependencyFactory;

class ContextContainer
{

    // Atributos de la clase ContextContainer

    /**
     *
     * @var ContextContainer 
     */
    protected static $instance;

    /**
     *
     * @var ClassBuilder 
     */
    private $builder;

    /**
     *
     * @var IDependencyFactory 
     */
    protected $factory;

    /**
     *
     * @var Json 
     */
    private $dependencies;

    /**
     *
     * @var Json 
     */
    private $shareds;

    // Constructor de la clase ContextContainer

    private function __construct(IJson $dependencies, IJson $shareds)
    {
        $this->dependencies = $dependencies;
        $this->shareds = $shareds;
    }

    // Métodos de la clase ContextContainer

    /**
     * 
     * @return ContextContainer
     */
    public static function getInstance(): ContextContainer
    {
        if (is_null(self::$instance)) {
            self::$instance = new static (new Json(), new Json());
        }

        return self::$instance;
    }

    /**
     * 
     * @param string $classFactory
     * @param string $classInstance
     * @return mixed
     */
    public function create(string $classFactory, string $classInstance)
    {
        $factory = $this->getFactory($classFactory);
        $builder = $factory->build($classInstance);

        if ($builder instanceof ClassInstance) {
            return $this->getBuilder()->create($builder, $this, $classFactory);
        }
        else {
            return $builder;
        }
    }

    /**
     * 
     * @param string $classDependency
     * @param mixed $value
     * @return void
     */
    public function attachShared(string $classDependency, $value): void
    {
        $this->shareds->attach($classDependency, $value);
    }

    /**
     * 
     * @param string $classDependency
     * @return mixed
     */
    public function getShared(string $classDependency)
    {
        return $this->shareds->getValue($classDependency);
    }

    /**
     * 
     * @param string $classFactory
     * @return IDependencyFactory
     */
    private function getFactory(string $classFactory): IDependencyFactory
    {
        if (is_null($this->factory)) {
            $this->factory = new $classFactory();
        }

        return $this->factory;
    }

    /**
     * 
     * @return ClassBuilder
     */
    private function getBuilder(): ClassBuilder
    {
        if (is_null($this->builder)) {
            $this->builder = $this->getInstanceBuilder();
        }

        return $this->builder;
    }

    /**
     * 
     * @return ClassBuilder
     */
    protected function getInstanceBuilder(): ClassBuilder
    {
        return new ClassBuilder();
    }
}

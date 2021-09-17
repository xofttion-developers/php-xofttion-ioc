<?php

namespace Xofttion\IoC;

use Xofttion\Kernel\Contracts\IJson;
use Xofttion\Kernel\Structs\Json;

class ClassInstance
{

    // Atributos de la clase ClassInstance

    /**
     *
     * @var string 
     */
    private $class;

    /**
     *
     * @var IJson 
     */
    private $dependencies;

    // Constructor de la clase ClassInstance

    /**
     * 
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->dependencies = new Json();
        $this->setClass($class);
    }

    // Métodos de la clase ClassInstance

    /**
     * 
     * @param string $class
     * @return void
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * 
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * 
     * @return mixed
     */
    public function getInstance()
    {
        return new $this->class();
    }

    /**
     * 
     * @param string $key
     * @param string $class
     * @param bool $shared
     * @return ClassInstance
     */
    public function attach(string $key, string $class, bool $shared = false): ClassInstance
    {
        $this->dependencies->attach($key, new Dependency($class, $shared));

        return $this;
    }

    /**
     * 
     * @return IJson
     */
    public function getDependencies(): IJson
    {
        return $this->dependencies;
    }

    /**
     * 
     * @param string $class
     * @param array $dependencies
     * @return ClassInstance
     */
    public static function build(string $class, array $dependencies = []): ClassInstance
    {
        $classInstance = new static ($class);

        foreach ($dependencies as $nameDependency => $classDependency) {
            $classInstance->attach($nameDependency, $classDependency);
        }

        return $classInstance;
    }
}

## Xofttion IoC

Librería que proporciona un conjunto de clase para implementar los principios de inversión de control (IoC).

## Instalación

    composer require xofttion/ioc

## Modo de uso

Supongamos que nos han pedido desarrollar una API para realizar las funciones de una calculadora, especificamente para hacer los procesos de suma, resta y multiplicación. Hemos decidido hacer una interfaz para dichas operaciones aritmeticas, lo cual nuestro código que así:

    interface IOperacionAritmetica {

    	public function ejecutar(int $a, int $b): int;
    }

    class Suma implements IOperacionAritmetica {

    	public function ejecutar(int $a, int $b): int {
    		return $a + $b;
    	}
    }

    class Resta implements IOperacionAritmetica {

    	public function ejecutar(int $a, int $b): int {
    		return $a - $b;
    	}
    }

    class Multiplicacion implements IOperacionAritmetica {

    	public function ejecutar(int $a, int $b): int {
    		return $a * $b;
    	}
    }

Para hacer mas didactico el ejercicio, hemos decidido crear una interfaz operador quien realmente se encargará de ejecutar la operación aritmetica, donde sus implementaciones recibiran por inyección de dependencia la respectiva operación correspondiente:

    interface IOperador {

    	public function ejecutar(int $a, int $b): int;
    }

    class Operador implements IOperador {

        private $operacion;

        public function setOperacion(IOperacionAritmetica $operacion): void {
            $this->operacion = $operacion;
        }

        public function ejecutar(int $a, int $b): int {
            return $this->operacion->ejecutar($a, $b);
        }
    }

    class Sumador extends Operador {
    }

    class Restador extends Operador {
    }

    class Multiplicador extends Operador {
    }

Con todo ya orquestado, empezaremos a definir la implementación de IoC, debemos primero es crear una fábrica `Xofttion\IoC\Contracts\IDependencyFactory`, donde se definiran las politicas para instanciar los objetos de clases con sus respectivas dependencias.

    use Xofttion\IoC\Contracts\IDependencyFactory;
    use Sumador;
    use Restador;
    use Multiplicador;
    use Suma;
    use Resta;
    use Multiplicacion;

    class Factory implements IDependencyFactory {

        public function build(string $class) {
            switch ($class) {
                case (Sumador::class):
    				$classInstance = new ClassInstance(Sumador::class);
    				$classInstance->attach("operacion", Suma::class);
                    return $classInstance;

                case (Restador::class):
    				$classInstance = new ClassInstance(Restador::class);
    				$classInstance->attach("operacion", Resta::class);
                    return $classInstance;

                case (Multiplicador::class):
    				$classInstance = new ClassInstance(Multiplicador::class);
    				$classInstance->attach("operacion", Multiplicacion::class);
                    return $classInstance;

                default:
    				return new $class();
            }
        }
    }

Para lograr esto debemos apoyarnos en la clase `Xofttion\IoC\ClassInstance` que nos permite definir la manera como se construirá una clase con sus respectivas dependencias.

Se debe establer nombre de clave de la dependencia y el tipo de clase a instanciar, donde la clave es un parámetro relevante para realizar esta funcionalidad, por lo que la librería
se basa en los principios de inyección de dependencias usando métodos tipo `setter`, por lo tanto en el ejemplo donde definimos que la clave es `operacion`, a la hora de construir el objeto se buscará la función `setOperacion` para inyectarle la respectiva dependencia.

Una vez definido nuestra fábrica de objetos, procedemos aplicar el proceso de IoC en concreto, para esto nos apoyamos en la clase `\Xofttion\IoC\ContextContainer` que es la
encargada de gestionar la construcción de los objetos de clases solicitados:

    use Xofttion\IoC\ContextContainer;
    use Factory;
    use Sumador;
    use Restador;
    use Multiplicador;

    $context = ContextContainer::getInstance();

    $sumador = $context->create(Factory::class, Sumador::class);
    $restador = $context->create(Factory::class, Restador::class);
    $multiplicador = $context->create(Factory::class, Multiplicador::class);

    echo "SUMA  (9+11) = {$sumador->ejecutar(9, 11)}";
    echo "RESTA (15-6) = {$restador->ejecutar(15, 6)}";
    echo "MULTIPLICACIÓN (4*2) = {$multiplicador->ejecutar(4, 2)}";

Con el ejercicio anterior hemos logrado generar todas las instancias de las funciones aritméticas, sin embargo ahora se requiere encapsular esto en una sola clase llamada `Calculadora`:

    use Sumador;
    use Restador;
    use Multiplicador;

    class Calculadora {

        private $sumador;

        private $restador;

        private $multiplicador;

        public function setSumador(IOperador $sumador): void {
            $this->sumador = $sumador;
        }

        public function setRestador(IOperador $restador): void {
            $this->restador = $restador;
        }

        public function setMultiplicador(IOperador $multiplicador): void {
            $this->multiplicador = $multiplicador;
        }

        public function sumar(int $a, int $b): int {
            return $this->sumador->ejecutar($a, $b);
        }

        public function restar(int $a, int $b): int {
            return $this->restador->ejecutar($a, $b);
        }

        public function multiplicar(int $a, int $b): int {
            return $this->multiplicador->ejecutar($a, $b);
        }
    }

Ahora vamos a nuestra fábrica y agregamos este nuevo caso:

    use Xofttion\IoC\Contracts\IDependencyFactory;
    use Sumador;
    use Restador;
    use Multiplicador;
    use Suma;
    use Resta;
    use Multiplicacion;
    use Calculadora;

    class Factory implements IDependencyFactory {

        public function build(string $class) {
            switch ($class) {
                case (Calculadora::class) :
    				$classInstance = new ClassInstance(Calculadora::class);
    				$classInstance->attach("sumador", Sumador::class);
    				$classInstance->attach("restador", Restador::class);
    				$classInstance->attach("multiplicador", Multiplicador::class);
                    return $classInstance;

                case (Sumador::class):
    				$classInstance = new ClassInstance(Sumador::class);
    				$classInstance->attach("operacion", Suma::class);
                    return $classInstance;

                case (Restador::class):
    				$classInstance = new ClassInstance(Restador::class);
    				$classInstance->attach("operacion", Resta::class);
                    return $classInstance;

                case (Multiplicador::class):
    				$classInstance = new ClassInstance(Multiplicador::class);
    				$classInstance->attach("operacion", Multiplicacion::class);
                    return $classInstance;

                default:
    				return new $class();
            }
        }
    }

Como podemos notar podemos definir las clases parametrizadas anteriormente como dependencias de esta nueva clase, la cual durante su etapa de construcción también inyectará las dependencias de sus dependencias.

De tal manera que ya podemos ejecutar nuestro programa de la siguiente manera:

    use Xofttion\IoC\ContextContainer;
    use Factory;
    use Calculadora;

    $context = ContextContainer::getInstance();

    $calculadora = $context->create(Factory::class, Calculadora::class);

    echo "SUMA (4+8)  = {$calculadora->sumar(4, 8)}";
    echo "RESTA (9-2) = {$calculadora->restar(9, 2)}";
    echo "MULTIPLICACIÓN (5*3) = {$calculadora->multiplicar(5, 3)}";

## Notas

La función `attach` de la clase `Xofttion\IoC\ClassInstance` puede recibir un tercer parámetro `$shared` de tipo `bool`, lo cual permite establecer que la instancia de la dependencia construida será
compartida entre las clases que la soliciten durante la ejecución de creación de objetos desde el `Xofttion\IoC\ContextContainer`.

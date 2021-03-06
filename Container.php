<?php

interface ContainerInterface
{
	public function get(string $abstract, $concrete);

	public function has($id);
}

class Container implements ContainerInterface
{

	protected $instances = [];

	public function set($abstract, $concentrate = NULL)
	{
		if ($concentrate === NULL) {
			$concentrate = $abstract;
		}
		$this->instances[$abstract] = $concentrate;
	}

	public function get($abstract, $parameters = [])
	{
		if (!isset($this->instances[$abstract])) {
			$this->set($abstract);
		}
		return $this->resolve($this->instances[$abstract], $parameters);
	}

	public function resolve($concrete, $parameters)
	{
		if ($concrete instanceof Closure) {
			return $concrete($this, $parameters);
		}
		$reflector = new ReflectionClass($concrete);

		if (!$reflector->isInstantiable()) {
			throw new Exception("Class {$concrete} is not instantiable");
		}
		$constructor = $reflector->getConstructor();

		if (is_null($constructor)) {
			return $reflector->newInstance();
		}

		$parameters   = $constructor->getParameters();
		$dependencies = $this->getDependencies($parameters);

		return $reflector->newInstanceArgs($dependencies);
	}

	public function has($id)
	{
		return $this->bound($id);
	}


	public function getDependencies($parameters)
	{
		$dependencies = [];
		foreach ($parameters as $parameter) {
			$dependency = $parameter->getClass();
			if ($dependency === NULL) {
				if ($parameter->isDefaultValueAvailable()) {
					$dependencies[] = $parameter->getDefaultValue();
				} else {
					throw new Exception("Can not resolve class dependency {$parameter->name}");
				}
			} else {
				$dependencies[] = $this->get($dependency->name);
			}
		}
		return $dependencies;
	}

	private function bound($abstract)
	{
		return isset($this->instances[$abstract]);
	}
}

class Profile {
   protected $setting;
   public function __construct(Setting $setting)
   {
      $this->setting = $setting;
   }
}

class Setting {}


$construct = new Container;
$construct->set('Oke');
$contain = $construct->get('Oke');
var_dump($contain);
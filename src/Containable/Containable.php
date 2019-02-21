<?php
namespace Containable;
use Exception;
use Psr\Container\{ContainerInterface,NotFoundExceptionInterface};

class NotFound extends Exception implements NotFoundExceptionInterface {}

class Containable implements ContainerInterface {
    private $storage = [];
    private $services = [];
    private $factories = [];

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @return mixed Entry.
     */
    public function get($id) {
        if (array_key_exists($id, $this->services)) {
            $callback = $this->services[$id];
            unset($this->services[$id]);
            $this->storage[$id] = call_user_func($callback, $this);
            return $this->storage[$id];
        }

        if (array_key_exists($id, $this->factories)) {
            return call_user_func($this->factories[$id], $this);
        }

        if (array_key_exists($id, $this->storage)) {
            return $this->storage[$id];
        }

        throw new NotFound(
            sprintf("Not Found: %s", print_r($id, true))
        );
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     * @return bool
     */
    public function has($id) {
        return array_key_exists($id, $this->storage);
    }

    /**
     * Sets a key in the container to the given value, object, function
     *
     * @param string $id Identifier of the entry
     * @param mixed $value Value to set for the key
     * @return Containable self
     *
     */
    public function set(string $id, $value) {
        $this->storage[$id] = $value;
        return $this;
    }

    /**
     * Register a service function that will be run once on-demand. This is
     * just like set, except it expects a callable, which will be called
     * once when the key is retrieved. The key will then be set to the value
     * that is returned from the callable.
     *
     * @param string $id Identifier of the entry
     * @param callable $callback
     */
    public function service(string $id, callable $callback) {
        $this->set($id, '');
        $this->services[$id] = $callback;
        return $this;
    }

    public function factory(string $id, callable $callback) {
        $this->set($id, '');
        $this->factories[$id] = $callback;
    }
}

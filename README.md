# Containable

A small DI container implementation in PHP. Conforms to the PSR11 interface
which it augments with services and factories.

### Usage

```php

$container = new Containable\Containable;

// Simple key value storage
$container->set('x', 1);
$container->has('x'); // => true
$container->get('x'); // => 1

// Store and call functions
$container->set('+', function($a, $b) {
    return $a + $b;
});

$container->get('+')(5, 7); // => 12

// Container-aware service function. This function is only called once on
// first use, and the returned value is stored for any subsequent calls
// to get()
$container->service('my-service', function($c) {
    return new Service($c->get('x'));
});

$container->get('my-service'); // => Singleton object

// Container-aware factory function. This function is called with the
// container on every call to get()
$container->factory('my-instance', function($c) {
    return new Instance($c->get('x'));
});

$container->get('my-instance'); // => New Object

```

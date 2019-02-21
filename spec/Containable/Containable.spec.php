<?php
namespace Containable;

describe('Containable\\Containable', function() {
    describe('set', function() {
        it('will set and get strings', function() {
            $container = new Containable;
            $container->set('abc', 'xyz');
            expect($container->get('abc'))->to->equal('xyz');
        });

        it('will report has and not has', function() {
            $container = new Containable;
            $container->set('abc', 'xyz');
            expect($container->has('abc'))->to->equal(true);
            expect($container->has('xyz'))->to->equal(false);
        });

        it('will throw error on invalid keys', function() {
            $container = new Containable;
            expect(function() use ($container) {
                $container->get('nothere');
            })->to->throw('Exception', 'Not Found: nothere');
        });

        it('will set and get functions', function() {
            $container = new Containable;
            $container->set('abc', function($a) {
                return $a + 1;
            });
            expect($container->get('abc')(1))->to->equal(2);
        });
    });

    describe('service', function() {
        it('will only run function once', function() {
            $container = new Containable;
            $container->service('svc', function($c) {
                return new \StdClass;
            });
            $a = $container->get('svc');
            $b = $container->get('svc');

            expect($a)->to->equal($b);
        });

        it('will lazy-load', function() {
            $container = new Containable;

            $container->service('svc', function($c) {
                return $c->get('abc');
            });

            $container->set('abc', 'xyz');

            expect($container->get('svc'))->to->equal('xyz');
        });
    });

    describe('factory', function() {
        it('will produce result on each call', function() {
            $container = new Containable;

            $container->factory('fac', function($c) {
                return new \StdClass;
            });
            $a = $container->get('fac');
            $b = $container->get('fac');

            expect($a)->to->not->equal($b);
        });
    });
});

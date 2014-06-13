<?php

namespace ApplicationTest;

// source: http://stackoverflow.com/a/8702347
// credits to
class PHPUnitUtil
{
    /**
     * Get a private or protected method for testing/documentation purposes.
     * How to use for MyClass->foo():
     *      $cls = new MyClass();
     *      $foo = PHPUnitUtil::getPrivateMethod($cls, 'foo');
     *      $foo->invoke($cls, $...);
     * @param object $obj The instantiated instance of your class
     * @param string $method The name of your private/protected method
     * @param array $args Method arguments
     * @return \ReflectionMethod The method you asked for
     */
    public static function callMethod($obj, $method, array $args) {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($method);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }
} 
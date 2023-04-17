<?php

namespace Reizzzor\Tools\Support;

use BadMethodCallException;

/**
 * Trait Fluent
 * @package Clickadilla\Support
 *
 * @property array $fluentMethods
 */
trait Fluent
{
    public function __call($method, $parameters)
    {
        if (array_key_exists($method, $this->fluentMethods)) {
            if (! count($parameters)) {
                return $this->fluentMethods[$method];
            }

            if (is_array($this->fluentMethods[$method])) {
                $this->fluentMethods[$method] = isset($parameters[0]) ? (array)$parameters[0] : [];
            } else {
                $this->fluentMethods[$method] = $parameters[0] ?? null;
            }

            return $this;
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
    }
}

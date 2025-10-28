<?php

declare(strict_types=1);

namespace App\Core;

class Container
{
    private array $bindings = [];
    private array $instances = [];
    private array $resolvingCallbacks = [];

    public function bind(string $abstract, $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
        $this->instances[$abstract] = null;
    }

    public function make(string $abstract)
    {
        // Check if we already have a resolved instance
        if (isset($this->instances[$abstract]) && $this->instances[$abstract] !== null) {
            return $this->instances[$abstract];
        }

        // Get the concrete implementation
        $concrete = $this->bindings[$abstract] ?? null;

        if ($concrete === null) {
            throw new \InvalidArgumentException("No binding found for {$abstract}");
        }

        // If it's a closure, resolve it
        if ($concrete instanceof \Closure) {
            $instance = $concrete($this);
        } else {
            $instance = $concrete;
        }

        // If it's a singleton, store the instance
        if (array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = $instance;
        }

        // Fire resolving callbacks
        $this->fireResolvingCallbacks($abstract, $instance);

        return $instance;
    }

    public function instance(string $abstract, $instance): void
    {
        $this->instances[$abstract] = $instance;
        $this->bindings[$abstract] = $instance;
    }

    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Register a resolving callback for an abstract.
     *
     * @param string $abstract
     * @param \Closure $callback
     * @return void
     */
    public function resolving(string $abstract, \Closure $callback): void
    {
        $this->resolvingCallbacks[$abstract][] = $callback;
    }

    /**
     * Fire the resolving callbacks for the given abstract.
     *
     * @param string $abstract
     * @param mixed $instance
     * @return void
     */
    private function fireResolvingCallbacks(string $abstract, $instance): void
    {
        if (isset($this->resolvingCallbacks[$abstract])) {
            foreach ($this->resolvingCallbacks[$abstract] as $callback) {
                $callback($instance, $this);
            }
        }

        // Fire global resolving callbacks
        if (isset($this->resolvingCallbacks['*'])) {
            foreach ($this->resolvingCallbacks['*'] as $callback) {
                $callback($instance, $this);
            }
        }
    }
}

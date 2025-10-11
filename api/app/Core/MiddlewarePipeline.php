<?php
declare(strict_types=1);

namespace App\Core;

class MiddlewarePipeline
{
    private array $middleware = [];
    private \Closure $destination;

    public function __construct()
    {
        $this->destination = function () {
            return null;
        };
    }

    /**
     * Set the destination (route handler).
     *
     * @param \Closure $destination
     * @return $this
     */
    public function send(\Closure $destination): self
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * Add middleware to the pipeline.
     *
     * @param MiddlewareInterface $middleware
     * @return $this
     */
    public function through(MiddlewareInterface $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    /**
     * Execute the middleware pipeline.
     *
     * @return mixed
     */
    public function then()
    {
        $runner = $this->carry($this->destination);
        return $runner();
    }

    /**
     * Carry the middleware pipeline.
     *
     * @param \Closure $destination
     * @return \Closure
     */
    private function carry(\Closure $destination)
    {
        return function () use ($destination) {
            $pipeline = array_reduce(
                array_reverse($this->middleware),
                $this->carrySlice(),
                $this->prepareDestination($destination)
            );
            
            return $pipeline();
        };
    }

    /**
     * Get the carry slice.
     *
     * @return \Closure
     */
    private function carrySlice(): \Closure
    {
        return function ($stack, $pipe) {
            return function () use ($stack, $pipe) {
                return $pipe->handle($stack);
            };
        };
    }

    /**
     * Prepare the destination closure.
     *
     * @param \Closure $destination
     * @return \Closure
     */
    private function prepareDestination(\Closure $destination): \Closure
    {
        return function () use ($destination) {
            return $destination();
        };
    }
}
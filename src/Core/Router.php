<?php
// src/Core/Router.php

final class Router
{
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, array $handler): void { $this->routes['GET'][$path] = $handler; }
    public function post(string $path, array $handler): void { $this->routes['POST'][$path] = $handler; }

    public function dispatch(string $method, string $path): void
    {
        error_log("ROUTER dispatch method=$method path=$path");
        error_log("ROUTER known GET routes=" . implode(',', array_keys($this->routes['GET'])));

        $method = strtoupper($method);
        $handler = $this->routes[$method][$path] ?? null;
        if (!$handler) {
            http_response_code(404);
            echo "404 - Page introuvable";
            return;
        }

        [$class, $action] = $handler;
        (new $class())->$action();
    }
}

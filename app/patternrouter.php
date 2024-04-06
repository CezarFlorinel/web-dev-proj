<?php
namespace App;

use Error;
use Exception;

class PatternRouter
{
    private function stripParameters($uri)
    {
        if (str_contains($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        return $uri;
    }

    public function route($uri)
    {
        // Check if the request is for the API
        $api = false;
        if (str_starts_with($uri, "api/")) {
            $uri = substr($uri, 4);
            $api = true;
        }

        $uri = $this->stripParameters($uri);

        $explodedUri = explode('/', $uri);

        // Default controller and method
        $defaultController = 'Home';
        $defaultMethod = 'index';

        if (!isset($explodedUri[0]) || empty($explodedUri[0])) {
            $explodedUri[0] = $defaultController;
        }

        $directory = $api ? 'App\\Api\\Controllers\\' : 'App\\Controllers\\';
        $controllerName = $directory . ucfirst($explodedUri[0]) . "Controller";

        if (!isset($explodedUri[1]) || empty($explodedUri[1])) {
            $explodedUri[1] = $defaultMethod;
        }

        $methodName = $explodedUri[1];

        // Controller/method matching the URL not found
        if (!class_exists($controllerName) || !method_exists($controllerName, $methodName)) {
            http_response_code(404);
            return;
        }

        try {
            $controllerObj = new $controllerName();
            $controllerObj->$methodName();
        } catch (Error $e) {
            echo "Error: " . $e->getMessage();
            http_response_code(500);
        }
    }

}
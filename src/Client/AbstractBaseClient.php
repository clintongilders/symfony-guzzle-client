<?php

namespace App\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * BaseClient class to be extended by specific API clients.
 * This class initializes the Guzzle client and loads the service description.
 */
abstract class AbstractBaseClient
{
    protected Description $description;
    protected Client $client;

    public function __construct(#[Autowire('%kernel.project_dir%')] private string $dir)
    {
        $this->loadDescriptionAndCreatClient();
    }

    abstract protected function loadDescriptionAndCreatClient(): void;

    protected function validateQueryParameter($param, $value, $name): void
    {
        switch ($param->getType()) {
            case 'integer':
                if (!is_numeric($value)) {
                    throw new \InvalidArgumentException("Query parameter {$name} must be an integer.");
                }
                break;
            case 'boolean':
                if (!is_bool($value)) {
                    throw new \InvalidArgumentException("Query parameter {$name} must be a boolean.");
                }
                break;
            case 'array':
                if (!is_array($value)) {
                    throw new \InvalidArgumentException("Query parameter {$name} must be an array.");
                }
                break;
            case 'string':
                if (!is_string($value)) {
                    throw new \InvalidArgumentException("Query parameter {$name} must be a string.");
                }
                break;
            default:
                throw new \InvalidArgumentException("Unsupported query parameter type: {$param->getType()}");
        }
    }

    protected function validateUriParameter($param, $value, $name): void
    {
        switch ($param->getType()) {
            case 'integer':
                if (!is_numeric($value)) {
                    throw new \InvalidArgumentException("URI parameter {$name} must be an integer.");
                }
                break;
            case 'string':
                if (!is_string($value)) {
                    throw new \InvalidArgumentException("URI parameter {$name} must be a string.");
                }
                break;
            default:
                throw new \InvalidArgumentException("Unsupported URI parameter type: {$param->getType()}");
        }
    }   

    protected function processConfiguration($configuration, $parameters): string
    {
        $uri = $configuration->getUri();
        
        if ($configuration->getParams() !== null) {
            foreach ($configuration->getParams() as $name => $param) {
                if (! isset($parameters[0][$name]) && $param->getRequired() === true) {
                    if ($param->default() === null) {
                        if ($param->getLocation() === 'uri') {
                            throw new \InvalidArgumentException("Missing required URI parameter: {$name}");
                        }
                        if ($param->getLocation() === 'query') {
                            throw new \InvalidArgumentException("Missing required query parameter: {$name}");
                        }
                        $parameters[0][$name] = $param->default();
                    }
                    throw new \InvalidArgumentException("Missing required parameter: {$name}");
                }
                
                if (isset($parameters[0][$name])) {
                    
                    if ($param->getLocation() === 'query') {
                        $this->validateQueryParameter($param, $parameters[0][$name], $name);
                    }
                    
                    if ($param->getLocation() === 'uri') {
                        $this->validateUriParameter($param, $parameters[0][$name], $name);
                        $uri = str_replace('{' . $name . '}', $parameters[0][$name], $uri);
                        continue;
                    }
                } else {
                    if ($param->getRequired() === true) {
                        throw new \InvalidArgumentException("Missing required parameter: {$name}");
                    }
                    continue;
                }
            }
        }
        return $uri;
    }

    public function __call($name,$parameters) {
        if ($this->description->getOperations() !== null && array_key_exists($name, $this->description->getOperations())) {
            $configuration = $this->description->getOperation($name);

            if ($configuration->getHttpMethod() === 'GET') {
                $uri = $this->processConfiguration($configuration, $parameters);
                $response = $this->client->get($uri, $parameters[0] ?? []);
                return json_decode($response->getBody()->getContents(), true);
            }

            if ($configuration->getHttpMethod() === 'POST') {
                $response = $this->client->post($configuration->getUri(), ['json' => $parameter]);
                return json_decode($response->getBody()->getContents(), true);
            }

            if ($configuration->getHttpMethod() === 'PUT') {
                $response = $this->client->put($configuration->getUri(), ['json' => $parameter]);
                return json_decode($response->getBody()->getContents(), true);
            }
        }

        throw new \BadMethodCallException("Method {$name} does not exist.");
    }
}

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

    public function __construct(#[Autowire('%kernel.project_dir%')]
        protected readonly string $dir)
    {
        $this->loadDescriptionAndCreateClient();
    }

    abstract protected function loadDescriptionAndCreateClient(): void;

    protected function validateQueryParameter(\GuzzleHttp\Command\Guzzle\Parameter $param, mixed $value, string $name): void
    {
        switch ($param->getType()) {
            case 'integer':
                if (!is_numeric($value)) {
                    throw new \InvalidArgumentException("Query parameter {$name} must be an integer.");
                }
                break;
            case 'boolean':
                if ($value !== 'true' && $value !== 1 && $value !== 'false' && $value !== 0) {
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

    protected function validateUriParameter(\GuzzleHttp\Command\Guzzle\Parameter $param, string|int $value, string|int $name): void
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

    /**
     * Processes the operation configuration and replaces URI parameters.
     *
     * @param \GuzzleHttp\Command\Guzzle\Operation $configuration The operation configuration.
     * @param array<mixed> $parameters The parameters provided for the operation.
     *
     * @return string The processed URI with parameters replaced.
     *
     * @throws \InvalidArgumentException If required parameters are missing or invalid.
     */
    protected function processConfiguration(\GuzzleHttp\Command\Guzzle\Operation $configuration, array $parameters): string
    {
        $uri = $configuration->getUri();

        if ($configuration->getParams()) {
            foreach ($configuration->getParams() as $name => $param) {
                if (! isset($parameters[0][$name]) && $param->isRequired() === true) {
                    if ($param->getDefault() === null) {
                        if ($param->getLocation() === 'uri') {
                            throw new \InvalidArgumentException("Missing required URI parameter: {$name}");
                        }
                        if ($param->getLocation() === 'query') {
                            throw new \InvalidArgumentException("Missing required query parameter: {$name}");
                        }
                        $parameters[0][$name] = $param->getDefault();
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
                    if ($param->isRequired() === true) {
                        throw new \InvalidArgumentException("Missing required parameter: {$name}");
                    }
                    continue;
                }
            }
        }
        return $uri;
    }

    /**
     * Magic method to handle dynamic method calls based on the service description.
     *
     * @param string $name The name of the method being called.
     * @param array<mixed> $parameters The parameters passed to the method.
     *
     * @return array<mixed> The response from the API call, decoded from JSON.
     *
     * @throws \BadMethodCallException If the method does not exist in the service description.
     * @throws \InvalidArgumentException If required parameters are missing or invalid.
     * @throws \GuzzleHttp\Exception\GuzzleException If the HTTP request fails.
     */
    public function __call($name, $parameters): array
    {
        if (array_key_exists($name, $this->description->getOperations())) {
            $configuration = $this->description->getOperation($name);

            if ($configuration->getHttpMethod() === 'GET') {
                $uri = $this->processConfiguration($configuration, $parameters);
                $response = $this->client->get($uri, ['query' => $parameters[0] ?? []]);
                return json_decode($response->getBody()->getContents(), true);
            }

            if ($configuration->getHttpMethod() === 'POST') {
                $response = $this->client->post($configuration->getUri(), ['json' => $parameters]);
                return json_decode($response->getBody()->getContents(), true);
            }

            if ($configuration->getHttpMethod() === 'PUT') {
                $response = $this->client->put($configuration->getUri(), ['json' => $parameters]);
                return json_decode($response->getBody()->getContents(), true);
            }
        }

        throw new \BadMethodCallException("Method {$name} does not exist.");
    }
}

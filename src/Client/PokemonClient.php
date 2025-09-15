<?php

namespace App\Client;

use GuzzleHttp\Client;
use App\DescriptionLoader\YamlLoader;
use GuzzleHttp\Command\Guzzle\Description;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Client\AbstractBaseClient;

/**
 * A client to interact with the Pokemon API.
 * API Documentation: https://pokeapi.co/docs/v2
 * This client uses Guzzle for HTTP requests.
 *
 * @method array<mixed> getPokemonById(array<mixed> $args = [])
 *   - id: (int) The ID of the pokemon to fetch. (required)
 * @method array<mixed> getPokemonByName(array<mixed> $args = [])
 *   - name: (string) The name of the pokemon to fetch. (required)
 * @method array<mixed> getPokemonList(array<mixed> $args = [])
 *   - limit: (int) Limit the number of results returned. (optional, default: 10)
 *   - offset: (int) The number of results to skip before starting to collect the result set. (optional, default: 0)
 */
class PokemonClient extends AbstractBaseClient
{
    protected Description $description;
    protected Client $client;
    private string $baseUri = 'https://pokeapi.co/api/v2/';

    protected function loadDescriptionAndCreateClient(): void
    {
        $loader = new YamlLoader(new FileLocator());
        $config = $loader->load($this->dir . '/config/guzzle-description/pokemon-client.yaml');
        $this->description = new Description($config);

        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'timeout'  => 5.0,
        ]);
    }
}

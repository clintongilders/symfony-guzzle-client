<?php

namespace App\Client;

use GuzzleHttp\Client;
use Guzzle\Service\Loader\YamlLoader;
use GuzzleHttp\Command\Guzzle\Description;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Client\AbstractBaseClient;

/**
 * A client to interact with the Pokemon API.
 * API Documentation: https://pokeapi.co/docs/v2
 * This client uses Guzzle for HTTP requests.
 *
 * @method
 */
class PokemonClient extends AbstractBaseClient
{
    protected Description $description;
    protected Client $client;
    private string $baseUri = 'https://pokeapi.co/api/v2/';

    public function __construct(#[Autowire('%kernel.project_dir%')]
 private readonly string $dir)
    {
        parent::__construct($dir);
    }
    protected function loadDescriptionAndCreatClient(): void
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

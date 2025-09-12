<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Client\PokemonClient;

#[AsCommand(
    name: 'app:pokemon',
    description: 'uses various pokemon API calls.',
    aliases: ['app:poke'],
)]
class PokemonCommand extends Command
{
    public function __construct(public PokemonClient $pokemonClient)
    {
        parent::__construct();
    }
    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        //$result = $this->pokemonClient->getPokemon(1);
        //$result2 = $this->pokemonClient->getPokemon('pikachu');
        //$params = [
        //    'limit' => 100,
        //    'offset' => 0
        //];
        $result5 = $this->pokemonClient->getPokemonList();
        //$output->writeln(print_r($result2, true));
        $output->writeln(print_r($result5, true));
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}

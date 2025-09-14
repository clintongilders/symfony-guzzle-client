<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Client\PokemonClient;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(
    name: 'app:pokemon',
    description: 'uses various pokemon API calls.',
    aliases: ['app:poke'],
)]
class PokemonCommand extends Command
{
    public function configure()
    {
        $this->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Limit the number of results', 10)
                ->addOption('offset', null, InputOption::VALUE_REQUIRED, 'Offset the results by this amount', 0)
                ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Name of the pokemon to fetch', 'pikachu')
                ->addOption('id', null, InputOption::VALUE_REQUIRED, 'ID of the pokemon to fetch', 1)
                ->addOption('sync-data', null, InputOption::VALUE_NONE, 'Refresh the data from the API');
    }
    public function __construct(public PokemonClient $pokemonClient)
    {
        parent::__construct();
    }
    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        /*
        $result = $this->pokemonClient->getPokemonById(['id' => $input->getOption('id')]);
        $output->writeln(print_r($result, true));
        $result2 = $this->pokemonClient->getPokemonByName(['name' => $input->getOption('name')]);
        $output->writeln(print_r($result2, true));
        */
        if ($input->getOption('limit')) {
            $limit = (int) $input->getOption('limit');
        } else {
            $limit = 10;
        }
        if ($input->getOption('offset')) {
            $offset = (int) $input->getOption('offset');
        } else {
            $offset = 0;
        }
        $result3 = $this->pokemonClient->getPokemonList(['limit' => $limit, 'offset' => $offset]);
        $output->writeln(print_r($result3, true));

        return Command::SUCCESS;
    }
}

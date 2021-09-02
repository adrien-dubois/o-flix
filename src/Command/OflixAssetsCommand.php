<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OflixAssetsCommand extends Command
{
    protected static $defaultName = 'oflix:assets';
    protected static $defaultDescription = 'Permets la création de deux dossiers : public/css, public/images';

    protected function configure(): void
    {
        $this
            ->addArgument('folder', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $folder = $input->getArgument('folder');

        if ($folder) {
            mkdir('public/' . $folder);
            $io->note('Le dossier ' . $folder . ' a bien été créé');
        }

        if ($input->getOption('option1')) {
            // ...
        }

  

        $io->success('Le dossier d\'asset a bien été créé');

        return Command::SUCCESS; 
    }
}

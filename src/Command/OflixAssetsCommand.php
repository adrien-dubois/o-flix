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
    protected static $defaultDescription = 'Permets la création de dossier asset';

    protected function configure(): void
    {
        $this
            ->addArgument('folder', InputArgument::REQUIRED, 'Le dossier à créer')
            ->addOption('addToGit', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $folder = $input->getArgument('folder');

        if ($folder) {
            $folderToCreate = 'public/' . $folder;
            mkdir($folderToCreate);

            $optionGit = $input->getOption('addToGit');
            if($optionGit){
                $keepFile = $folderToCreate . '/.keep';

                file_put_contents($keepFile, 'Je suis ton père');
            }

            $io->note('Le dossier ' . $folder . ' a bien été créé');
        }

        $io->success('Le dossier d\'asset a bien été créé');

        return Command::SUCCESS; 
    }
}

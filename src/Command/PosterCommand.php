<?php

namespace App\Command;

use App\Repository\TvShowRepository;
use App\Service\OmdbApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PosterCommand extends Command
{
    // Name of the commande in the terminal
    protected static $defaultName = 'tvshow:poster';
    protected static $defaultDescription = 'Mets à jour les posters de toutes les séries à partir de l\'API';

    protected function configure(): void
    {
        // Ici on mets à jour uniquement la série dont l'ID est 2
        // et au passage on met à jour la colonne updatedAt
        // php bin/console tvshow:poster 2 --updatedAt
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    
    }

    private $omdbApi;
    private $tvShowRepository;
    private $manager;

    public function __construct(OmdbApi $omdbApi, TvShowRepository $tvShowRepository, EntityManagerInterface $entityManager )
    {
        $this->tvShowRepository = $tvShowRepository;

        // On récupérer les posters pour chaque série
        $this->omdbApi = $omdbApi;

        // On va sauvegarder nos séries en BDD
        $this->manager = $entityManager;

        // On appelle le contructeur de la class parenr
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        // On va mettre la propriété image à partir de donées issues de OmbdApi

        // STEP 1 : We get all series in DB 
        $tvShowList = $this->tvShowRepository->findAll();

        // Step II : For echa tv show, we ask OmdbApi et get back the poster's show

        foreach ($tvShowList as $tvShow){
            $title = $tvShow->getTitle();
            $tvShowData = $this->omdbApi->fetch($title);
            if(isset($tvShowData['Poster'])) {
                $tvShow->setImage($tvShowData['Poster']);
            }
            $io->text('Mise à jour de la série' . $title . ' en cours...');
        }

        

        // Step III : We call the manager to save shows in DB

        $this->manager->flush();

        $io->success('Mise à jour de toutes les séries, effectuée avec succès');

        return Command::SUCCESS;
    }
}

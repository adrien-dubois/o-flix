<?php

namespace App\Command;

use App\Repository\TvShowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

class TvshowSluggerCommand extends Command
{
    protected static $defaultName = 'tvshow:slugger';
    protected static $defaultDescription = 'Ajoute un slug aux séries TV';

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }


    private $repository;
    private $em;
    private $slugger;

    public function __construct(TvShowRepository $repository, EntityManagerInterface $em, SluggerInterface $slugger )
    {   
        $this->repository = $repository;
        $this->em = $em;
        $this->slugger = $slugger;

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
        //     ...
        // }

        $tvShowList = $this->repository->findAll();

        foreach($tvShowList as $tvShow){
            $title = $tvShow->getTitle();
            $slug = $this->slugger->slug(strtolower($title));
            if (isset($slug)) {
                $tvShow->setSlug($slug);
            }
            $io->text('Création du slug de ' . $title);
        }
        $this->em->flush();
        
        $io->success('Création et mise à jour des slugs réussie');

        return Command::SUCCESS;
    }
}

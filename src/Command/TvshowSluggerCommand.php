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
            ->addArgument('id', InputArgument::OPTIONAL, 'ID de la série du slug à mettre à jour')
            ->addOption('update', '-u' , InputOption::VALUE_NONE, 'Mets à jour la propriété updatedAt')
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
        $id = $input->getArgument('id');
        $update = $input->getOption('update');

        if ($id) {
            $tvShowSingle = $this->repository->find($id);
            $title = $tvShowSingle->getTitle();
            $slug = $this->slugger->slug(strtolower($title));
            if(isset($slug)){
                $tvShowSingle->setSlug($slug);
            }
            $optionUpdate = $input->getOption('update');
            if($optionUpdate){
                $tvShowSingle->setUpdatedAt(new \DateTimeImmutable());
            }

            $io->text('Mise à jour du slug de la série ' . $title);

        } else {

            $tvShowList = $this->repository->findAll();

            foreach ($tvShowList as $tvShow) {
                $title = $tvShow->getTitle();
                $slug = $this->slugger->slug(strtolower($title));
                if (isset($slug)) {
                    $tvShow->setSlug($slug);
                }
                $io->text('Création du slug de ' . $title);
            }
        }
        $this->em->flush();
        $io->success('Création et mise à jour des slugs réussie');

        return Command::SUCCESS;
    }
}

<?php

namespace AppBundle\Command;

use AppBundle\Entity\Clipping;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CircusConvertEntitiesCommand command.
 */
class ConvertEntitiesCommand extends ContainerAwareCommand
{
    const BATCH_SIZE = 100;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em) {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName('circus:convert:entities')
            ->setDescription('Convert encoded entities to utf8.')
        ;
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input Command input, as defined in the configure() method.
     * @param OutputInterface $output Output destination.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('e')->from(Clipping::class, 'e')->where('e.edition is not null');
        $iterator = $qb->getQuery()->iterate();
        $matches = array();
        while($row = $iterator->next()) {
            $title = $row[0];
            if(preg_match('/^(\d+)/', $title, $matches)) {
                $title->setEditionNumber($matches[1]);
                $this->em->flush();
                $this->em->clear();
            }
        }
    }

}

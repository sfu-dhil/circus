<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\Clipping;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CircusConvertEntitiesCommand command.
 */
class ConvertEntitiesCommand extends Command {
    public const BATCH_SIZE = 100;

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
    protected function configure() : void {
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
    protected function execute(InputInterface $input, OutputInterface $output) : void {
        $qb = $this->em->createQueryBuilder();
        $qb->select('e')->from(Clipping::class, 'e')->where('e.edition is not null');
        $iterator = $qb->getQuery()->iterate();
        $matches = [];
        while ($row = $iterator->next()) {
            $title = $row[0];
            if (preg_match('/^(\d+)/', $title, $matches)) {
                $title->setEditionNumber($matches[1]);
                $this->em->flush();
                $this->em->clear();
            }
        }
    }
}

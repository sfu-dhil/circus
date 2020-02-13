<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\Clipping;
use App\Services\Thumbnailer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateThumbnailsCommand extends ContainerAwareCommand {
    protected function configure() : void {
        $this
            ->setName('app:generate-thumbnails')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void {
        $thumbnailer = $this->getContainer()->get(Thumbnailer::class);
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repo = $em->getRepository(Clipping::class);
        foreach ($repo->findAll() as $clipping) {
            $output->writeln($clipping->getOriginalName());
            $clipping->setThumbnailPath($thumbnailer->thumbnail($clipping));
            $em->flush();
        }
    }
}

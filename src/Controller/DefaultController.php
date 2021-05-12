<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\BlogBundle\Entity\Page;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Show the home page.
     *
     * @Route("/", name="homepage", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $pageRepo = $em->getRepository(Page::class);

        return [
            'homepage' => $pageRepo->findHomepage(),
        ];
    }

    /**
     * Show the privacy page.
     *
     * @Route("/privacy", name="privacy", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function privacyAction(Request $request) {
        return [];
    }
}

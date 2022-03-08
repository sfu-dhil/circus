<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Source;
use App\Form\SourceType;
use App\Repository\ClippingRepository;
use App\Repository\SourceRepository;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/source")
 */
class SourceController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="source_index", methods={"GET"})
     */
    public function index(Request $request, SourceRepository $sourceRepository) : Response {
        $query = $sourceRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return $this->render('source/index.html.twig', [
            'sources' => $this->paginator->paginate($query, $page, $pageSize),
        ]);
    }

    /**
     * @Route("/new", name="source_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     */
    public function new(Request $request, EntityManagerInterface $em) : Response {
        $source = new Source();
        $form = $this->createForm(SourceType::class, $source);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($source);
            $em->flush();
            $this->addFlash('success', 'The new source has been saved.');

            return $this->redirectToRoute('source_show', ['id' => $source->getId()]);
        }

        return $this->render('source/new.html.twig', [
            'source' => $source,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="source_show", methods={"GET"})
     */
    public function show(Request $request, Source $source, ClippingRepository $repo) : Response {
        $pageSize = (int) $this->getParameter('page_size');
        $query = $repo->sourceQuery($source);
        $clippings = $this->paginator->paginate($query, $request->query->getint('page', 1), $pageSize);

        return $this->render('source/show.html.twig', [
            'source' => $source,
            'clippings' => $clippings,
        ]);
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="source_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Source $source, EntityManagerInterface $em) : Response {
        $form = $this->createForm(SourceType::class, $source);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The updated source has been saved.');

            return $this->redirectToRoute('source_show', ['id' => $source->getId()]);
        }

        return $this->render('source/edit.html.twig', [
            'source' => $source,
            'edit_form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="source_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Source $source, EntityManagerInterface $em) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $source->getId(), $request->request->get('_token'))) {
            $em->remove($source);
            $em->flush();
            $this->addFlash('success', 'The source has been deleted.');
        } else {
            $this->addFlash('warning', 'The security token was not valid.');
        }

        return $this->redirectToRoute('source_index');
    }
}

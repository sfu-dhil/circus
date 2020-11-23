<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Clipping;
use App\Entity\Source;
use App\Form\SourceType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Source controller.
 *
 * @Route("/source")
 */
class SourceController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Source entities.
     *
     * @Route("/", name="source_index", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Source::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $sources = $this->paginator->paginate($query, $request->query->getint('page', 1), 24);

        return [
            'sources' => $sources,
        ];
    }

    /**
     * Creates a new Source entity.
     *
     * @Route("/new", name="source_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @Template
     *
     * @return array | RedirectResponse
     */
    public function newAction(Request $request, EntityManagerInterface $em) {
        $source = new Source();
        $form = $this->createForm(SourceType::class, $source);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($source);
            $em->flush();

            $this->addFlash('success', 'The new source was created.');

            return $this->redirectToRoute('source_show', ['id' => $source->getId()]);
        }

        return [
            'source' => $source,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Source entity.
     *
     * @Route("/{id}", name="source_show", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function showAction(Request $request, Source $source, EntityManagerInterface $em) {
        $repo = $em->getRepository(Clipping::class);
        $query = $repo->sourceQuery($source);

        $clippings = $this->paginator->paginate($query, $request->query->getint('page', 1), 24);

        return [
            'source' => $source,
            'clippings' => $clippings,
        ];
    }

    /**
     * Displays a form to edit an existing Source entity.
     *
     * @Route("/{id}/edit", name="source_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @Template
     *
     * @return array | RedirectResponse
     */
    public function editAction(Request $request, Source $source, EntityManagerInterface $em) {
        $editForm = $this->createForm(SourceType::class, $source);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The source has been updated.');

            return $this->redirectToRoute('source_show', ['id' => $source->getId()]);
        }

        return [
            'source' => $source,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Source entity.
     *
     * @Route("/{id}/delete", name="source_delete", methods={"GET"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Source $source, EntityManagerInterface $em) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($source);
        $em->flush();
        $this->addFlash('success', 'The source was deleted.');

        return $this->redirectToRoute('source_index');
    }
}

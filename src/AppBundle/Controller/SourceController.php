<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Clipping;
use AppBundle\Entity\Source;
use AppBundle\Form\SourceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Source controller.
 *
 * @Route("/source")
 */
class SourceController extends Controller {

    /**
     * Lists all Source entities.
     *
     * @Route("/", name="source_index", methods={"GET"})

     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Source::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $sources = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'sources' => $sources,
        );
    }

    /**
     * Creates a new Source entity.
     *
     * @Route("/new", name="source_new", methods={"GET","POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")

     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request) {
        $source = new Source();
        $form = $this->createForm(SourceType::class, $source);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($source);
            $em->flush();

            $this->addFlash('success', 'The new source was created.');
            return $this->redirectToRoute('source_show', array('id' => $source->getId()));
        }

        return array(
            'source' => $source,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Source entity.
     *
     * @Route("/{id}", name="source_show", methods={"GET"})

     * @Template()
     * @param Source $source
     * @param Request $request
     */
    public function showAction(Request $request, Source $source) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Clipping::class);
        $query = $repo->sourceQuery($source);
        $paginator = $this->get('knp_paginator');
        $clippings = $paginator->paginate($query, $request->query->getint('page', 1), 25);
        
        return array(
            'source' => $source,
            'clippings' => $clippings,
        );
    }

    /**
     * Displays a form to edit an existing Source entity.
     *
     * @Route("/{id}/edit", name="source_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")

     * @Template()
     * @param Request $request
     * @param Source $source
     */
    public function editAction(Request $request, Source $source) {
        $editForm = $this->createForm(SourceType::class, $source);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The source has been updated.');
            return $this->redirectToRoute('source_show', array('id' => $source->getId()));
        }

        return array(
            'source' => $source,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Source entity.
     *
     * @Route("/{id}/delete", name="source_delete", methods={"GET"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @param Request $request
     * @param Source $source
     */
    public function deleteAction(Request $request, Source $source) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($source);
        $em->flush();
        $this->addFlash('success', 'The source was deleted.');

        return $this->redirectToRoute('source_index');
    }

}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Clipping;
use AppBundle\Form\ClippingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Clipping controller.
 *
 * @Route("/clipping")
 */
class ClippingController extends Controller {
    /**
     * Lists all Clipping entities.
     *
     * @Route("/", name="clipping_index", methods={"GET"})
     *
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Clipping::class, 'e')->orderBy('e.date', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $clippings = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'clippings' => $clippings,
        );
    }

    /**
     * Search for Clipping entities.
     *
     * @Route("/search", name="clipping_search", methods={"GET"})
     *
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Clipping');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $clippings = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $clippings = array();
        }

        return array(
            'clippings' => $clippings,
            'q' => $q,
        );
    }

    /**
     * Creates a new Clipping entity.
     *
     * @Route("/new", name="clipping_new", methods={"GET","POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Template()
     *
     * @param Request $request
     *
     * @return array | RedirectResponse
     */
    public function newAction(Request $request) {
        $clipping = new Clipping();
        $form = $this->createForm(ClippingType::class, $clipping);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clipping);
            $em->flush();

            $this->addFlash('success', 'The new clipping was created.');

            return $this->redirectToRoute('clipping_show', array('id' => $clipping->getId()));
        }

        return array(
            'clipping' => $clipping,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Clipping entity.
     *
     * @Route("/{id}", name="clipping_show", methods={"GET"})
     *
     * @Template()
     *
     * @param Clipping $clipping
     *
     * @return array
     */
    public function showAction(Clipping $clipping) {
        return array(
            'clipping' => $clipping,
        );
    }

    /**
     * Displays a form to edit an existing Clipping entity.
     *
     * @Route("/{id}/edit", name="clipping_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @Template()
     *
     * @param Request $request
     * @param Clipping $clipping
     *
     * @return array | RedirectResponse
     */
    public function editAction(Request $request, Clipping $clipping) {
        $editForm = $this->createForm(ClippingType::class, $clipping);
        $editForm->remove('imageFile');
        $editForm->add('newImageFile', FileType::class, array(
            'label' => 'New Image',
            'required' => false,
            'attr' => array(
                'help_block' => 'Select a file to replace the current one. Optional.',
            ),
            'mapped' => false,
        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if (($upload = $editForm->get('newImageFile')->getData())) {
                $clipping->setImageFile($upload);
                $clipping->preUpdate(); // force doctrine to update.
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The clipping has been updated.');

            return $this->redirectToRoute('clipping_show', array('id' => $clipping->getId()));
        }

        return array(
            'clipping' => $clipping,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Clipping entity.
     *
     * @Route("/{id}/delete", name="clipping_delete", methods={"GET"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @param Request $request
     * @param Clipping $clipping
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Clipping $clipping) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($clipping);
        $em->flush();
        $this->addFlash('success', 'The clipping was deleted.');

        return $this->redirectToRoute('clipping_index');
    }
}

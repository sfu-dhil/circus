<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Clipping;
use AppBundle\Form\ClippingType;

/**
 * Clipping controller.
 *
 * @Route("/clipping")
 */
class ClippingController extends Controller {

    /**
     * Lists all Clipping entities.
     *
     * @Route("/", name="clipping_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Clipping::class, 'e')->orderBy('e.id', 'ASC');
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
     * To make this work, add a method like this one to the 
     * AppBundle:Clipping repository. Replace the fieldName with
     * something appropriate, and adjust the generated search.html.twig
     * template.
     * 
      //    public function searchQuery($q) {
      //        $qb = $this->createQueryBuilder('e');
      //        $qb->where("e.fieldName like '%$q%'");
      //        return $qb->getQuery();
      //    }
     *
     *
     * @Route("/search", name="clipping_search")
     * @Method("GET")
     * @Template()
     * @param Request $request
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
     * Full text search for Clipping entities.
     *
     * To make this work, add a method like this one to the 
     * AppBundle:Clipping repository. Replace the fieldName with
     * something appropriate, and adjust the generated fulltext.html.twig
     * template.
     * 
      //    public function fulltextQuery($q) {
      //        $qb = $this->createQueryBuilder('e');
      //        $qb->addSelect("MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') as score");
      //        $qb->add('where', "MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') > 0.5");
      //        $qb->orderBy('score', 'desc');
      //        $qb->setParameter('q', $q);
      //        return $qb->getQuery();
      //    }
     * 
     * Requires a MatchAgainst function be added to doctrine, and appropriate
     * fulltext indexes on your Clipping entity.
     *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
     *
     *
     * @Route("/fulltext", name="clipping_fulltext")
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function fulltextAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Clipping');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->fulltextQuery($q);
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
     * @Route("/new", name="clipping_new")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $clipping = new Clipping();
        $form = $this->createForm(ClippingType::class, $clipping);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {            
            $file = $clipping->getImageFile();
            $filename = md5(uniqid()) . '.' . $file->guessExtension();            
            $file->move($this->getParameter('clipping_img_dir'), $filename);
            $clipping->setImageFilePath($filename);
            $clipping->setOriginalName($file->getClientOriginalName());
            $clipping->setImageSize($file->getClientSize());
            $dimensions = getimagesize($this->getParameter('clipping_img_dir') . '/' . $filename);
            $clipping->setImageWidth($dimensions[0]);
            $clipping->setImageHeight($dimensions[1]);
            
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
     * @Route("/{id}", name="clipping_show")
     * @Method("GET")
     * @Template()
     * @param Clipping $clipping
     */
    public function showAction(Clipping $clipping) {

        return array(
            'clipping' => $clipping,
        );
    }

    /**
     * Displays a form to edit an existing Clipping entity.
     *
     * @Route("/{id}/edit", name="clipping_edit")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param Clipping $clipping
     */
    public function editAction(Request $request, Clipping $clipping) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $editForm = $this->createForm(ClippingType::class, $clipping);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
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
     * @Route("/{id}/delete", name="clipping_delete")
     * @Method("GET")
     * @param Request $request
     * @param Clipping $clipping
     */
    public function deleteAction(Request $request, Clipping $clipping) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($clipping);
        $em->flush();
        $this->addFlash('success', 'The clipping was deleted.');

        return $this->redirectToRoute('clipping_index');
    }

}

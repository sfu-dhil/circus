<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Clipping;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Category controller.
 *
 * @Route("/category")
 */
class CategoryController extends Controller {
    /**
     * Lists all Category entities.
     *
     * @Route("/", name="category_index", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Category::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $categories = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'categories' => $categories,
        ];
    }

    /**
     * Creates a new Category entity.
     *
     * @Route("/new", name="category_new", methods={"GET","POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Template()
     *
     * @return array | RedirectResponse
     */
    public function newAction(Request $request) {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'The new category was created.');

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return [
            'category' => $category,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Category entity.
     *
     * @Route("/{id}", name="category_show", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function showAction(Request $request, Category $category) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Clipping::class);
        $query = $repo->categoryQuery($category);
        $paginator = $this->get('knp_paginator');
        $clippings = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'category' => $category,
            'clippings' => $clippings,
        ];
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="category_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Template()
     *
     * @return array | RedirectResponse
     */
    public function editAction(Request $request, Category $category) {
        $editForm = $this->createForm(CategoryType::class, $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The category has been updated.');

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return [
            'category' => $category,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Category entity.
     *
     * @Route("/{id}/delete", name="category_delete", methods={"GET"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Category $category) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'The category was deleted.');

        return $this->redirectToRoute('category_index');
    }
}

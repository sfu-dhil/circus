<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Clipping;
use App\Form\ClippingSearchType;
use App\Form\ClippingType;
use App\Repository\ClippingRepository;

use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/clipping")
 */
class ClippingController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="clipping_index", methods={"GET"})
     */
    public function index(Request $request, ClippingRepository $clippingRepository) : Response {
        $query = $clippingRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return $this->render('clipping/index.html.twig', [
            'clippings' => $this->paginator->paginate($query, $page, $pageSize),
        ]);
    }

    /**
     * @Route("/search", name="clipping_search", methods={"GET"})
     */
    public function search(Request $request, ClippingRepository $repo) : Response {
        $form = $this->createForm(ClippingSearchType::class, null, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);
        $clippings = [];
        $submitted = false;
        $q = '';

        if ($form->isSubmitted() && $form->isValid()) {
            $submitted = true;
            $q = $form->get('transcription')->getData();
            $query = $repo->searchQuery($form->getData());
            $clippings = $this->paginator->paginate($query, $request->query->getInt('page', 1), 24);
        }

        return $this->render('clipping/search.html.twig', [
            'submitted' => $submitted,
            'clippings' => $clippings,
            'form' => $form->createView(),
            'q' => $q,
        ]);
    }

    /**
     * @Route("/new", name="clipping_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_CONTENT_ADMIN")
     */
    public function new(Request $request, EntityManagerInterface $em, FileUploader $uploader) : Response {
        $clipping = new Clipping();
        $form = $this->createForm(ClippingType::class, $clipping);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploader->processClipping($clipping);
            $em->persist($clipping);
            $em->flush();
            $this->addFlash('success', 'The new clipping has been saved.');

            return $this->redirectToRoute('clipping_show', ['id' => $clipping->getId()]);
        }

        return $this->render('clipping/new.html.twig', [
            'clipping' => $clipping,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="clipping_show", methods={"GET"})
     */
    public function show(Clipping $clipping) : Response {
        return $this->render('clipping/show.html.twig', [
            'clipping' => $clipping,
        ]);
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="clipping_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Clipping $clipping, EntityManagerInterface $em, FileUploader $uploader) : Response {        $form = $this->createForm(ClippingType::class, $clipping);
        $this->createForm(ClippingType::class, $clipping);
        $form->remove('imageFile');
        $form->add('newImageFile', FileType::class, [
            'label' => 'New Image',
            'required' => false,
            'attr' => [
                'help_block' => 'Select a file to replace the current one. Optional.',
            ],
            'mapped' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (($upload = $form->get('newImageFile')->getData())) {
                $clipping->setImageFile($upload);
                $uploader->processClipping($clipping);
            }
            $em->flush();
            $this->addFlash('success', 'The updated clipping has been saved.');

            return $this->redirectToRoute('clipping_show', ['id' => $clipping->getId()]);
        }

        return $this->render('clipping/edit.html.twig', [
            'clipping' => $clipping,
            'edit_form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="clipping_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Clipping $clipping, EntityManagerInterface $em) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $clipping->getId(), $request->request->get('_token'))) {
            $em->remove($clipping);
            $em->flush();
            $this->addFlash('success', 'The clipping has been deleted.');
        } else {
            $this->addFlash('warning', 'The security token was not valid.');
        }

        return $this->redirectToRoute('clipping_index');
    }
}

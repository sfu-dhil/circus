<?php

declare(strict_types=1);

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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Clipping controller.
 */
#[Route(path: '/clipping')]
class ClippingController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'clipping_index', methods: ['GET'])]
    #[Template]
    public function index(Request $request, EntityManagerInterface $em) : array {
        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->addSelect('CAST(e.number as integer) HIDDEN n')
            ->from(Clipping::class, 'e')
            ->orderBy('n', 'ASC')
            ->addOrderBy('e.date', 'ASC')
            ->addOrderBy('e.id', 'ASC')
        ;
        $query = $qb->getQuery();
        $clippings = $this->paginator->paginate($query, $request->query->getint('page', 1), 24);

        return [
            'clippings' => $clippings,
        ];
    }

    #[Route(path: '/search', name: 'clipping_search', methods: ['GET'])]
    #[Template]
    public function search(Request $request, ClippingRepository $repo) : array {
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

        return [
            'submitted' => $submitted,
            'clippings' => $clippings,
            'form' => $form->createView(),
            'q' => $q,
        ];
    }

    #[Route(path: '/new', name: 'clipping_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Template]
    public function new(Request $request, EntityManagerInterface $em, FileUploader $uploader) : array|RedirectResponse {
        $clipping = new Clipping();
        $form = $this->createForm(ClippingType::class, $clipping);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploader->processClipping($clipping);
            $em->persist($clipping);
            $em->flush();

            $this->addFlash('success', 'The new clipping was created.');

            return $this->redirectToRoute('clipping_show', ['id' => $clipping->getId()]);
        }

        return [
            'clipping' => $clipping,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'clipping_show', methods: ['GET'])]
    #[Template]
    public function show(Clipping $clipping) : array {
        return [
            'clipping' => $clipping,
        ];
    }

    #[Route(path: '/{id}/edit', name: 'clipping_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Template]
    public function edit(Request $request, Clipping $clipping, EntityManagerInterface $em, FileUploader $uploader) : array|RedirectResponse {
        $editForm = $this->createForm(ClippingType::class, $clipping);
        $editForm->remove('imageFile');
        $editForm->add('newImageFile', FileType::class, [
            'label' => 'New Image',
            'required' => false,
            'attr' => [
                'help_block' => 'Select a file to replace the current one. Optional.',
            ],
            'mapped' => false,
        ]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($upload = $editForm->get('newImageFile')->getData()) {
                $clipping->setImageFile($upload);
                $uploader->processClipping($clipping);
            }
            $em->flush();
            $this->addFlash('success', 'The clipping has been updated.');

            return $this->redirectToRoute('clipping_show', ['id' => $clipping->getId()]);
        }

        return [
            'clipping' => $clipping,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Clipping entity.
     */
    #[Route(path: '/{id}/delete', name: 'clipping_delete', methods: ['GET'])]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    public function delete(Clipping $clipping, EntityManagerInterface $em) : RedirectResponse {
        $em->remove($clipping);
        $em->flush();
        $this->addFlash('success', 'The clipping was deleted.');

        return $this->redirectToRoute('clipping_index');
    }
}

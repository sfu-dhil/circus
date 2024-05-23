<?php

declare(strict_types=1);

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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Source controller.
 */
#[Route(path: '/source')]
class SourceController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'source_index', methods: ['GET'])]
    #[Template]
    public function index(Request $request, EntityManagerInterface $em) : array {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Source::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $sources = $this->paginator->paginate($query, $request->query->getint('page', 1), 24);

        return [
            'sources' => $sources,
        ];
    }

    #[Route(path: '/new', name: 'source_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Template]
    public function new(Request $request, EntityManagerInterface $em) : array|RedirectResponse {
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

    #[Route(path: '/{id}', name: 'source_show', methods: ['GET'])]
    #[Template]
    public function show(Request $request, Source $source, EntityManagerInterface $em) : array {
        $repo = $em->getRepository(Clipping::class);
        $query = $repo->sourceQuery($source);

        $clippings = $this->paginator->paginate($query, $request->query->getint('page', 1), 24);

        return [
            'source' => $source,
            'clippings' => $clippings,
        ];
    }

    #[Route(path: '/{id}/edit', name: 'source_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Template]
    public function edit(Request $request, Source $source, EntityManagerInterface $em) : array|RedirectResponse {
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

    #[Route(path: '/{id}/delete', name: 'source_delete', methods: ['GET'])]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    public function delete(Source $source, EntityManagerInterface $em) : RedirectResponse {
        $em->remove($source);
        $em->flush();
        $this->addFlash('success', 'The source was deleted.');

        return $this->redirectToRoute('source_index');
    }
}

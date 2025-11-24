<?php

declare(strict_types=1);

namespace App\Menu;

use App\Repository\SourceRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class to build some menus for navigation.
 */
class Builder implements ContainerAwareInterface {
    use ContainerAwareTrait;

    public const CARET = ' ▾'; // U+25BE, black down-pointing small triangle.

    public function __construct(
        private FactoryInterface $factory,
        private AuthorizationCheckerInterface $authChecker,
        private TokenStorageInterface $tokenStorage,
        private Packages $packages,
        private SourceRepository $sourceRepository
    ) {}

    /**
     * @param string $role
     *
     * @return bool
     */
    private function hasRole($role) {
        if ( ! $this->tokenStorage->getToken()) {
            return false;
        }

        return $this->authChecker->isGranted($role);
    }

    /**
     * Build a menu for blog posts.
     *
     * @return ItemInterface
     */
    public function mainMenu(array $options) {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes([
            'class' => 'nav navbar-nav',
        ]);

        $menu->addChild('home', [
            'label' => 'Welcome',
            'route' => 'homepage',
            'attributes' => [
                'class' => 'nav-item',
            ],
            'linkAttributes' => [
                'class' => 'nav-link',
            ],
        ]);

        $browse = $menu->addChild('browse', [
            'uri' => '#',
            'label' => 'Archive',
            'attributes' => [
                'class' => 'nav-item dropdown',
            ],
            'linkAttributes' => [
                'class' => 'nav-link dropdown-toggle',
                'role' => 'button',
                'data-bs-toggle' => 'dropdown',
                'id' => 'browse-dropdown',
            ],
            'childrenAttributes' => [
                'class' => 'dropdown-menu text-small shadow',
                'aria-labelledby' => 'browse-dropdown',
            ],
        ]);

        $sources = $this->sourceRepository->findBy([], ['id' => 'ASC']);

        foreach ($sources as $source) {
            $browse->addChild('astley_' . $source->getId(), [
                'label' => $source->getLabel(),
                'route' => 'source_show',
                'routeParameters' => ['id' => $source->getId()],
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);
        }

        $browse->addChild('categories', [
            'label' => 'Categories',
            'route' => 'category_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);

        if ($this->hasRole('ROLE_USER')) {
            $browse->addChild('divider1', [
                'label' => '',
                'attributes' => [
                    'role' => 'separator',
                    'class' => 'divider',
                ],
            ]);
            $browse->addChild('categories', [
                'label' => 'Categories',
                'route' => 'category_index',
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);
            $browse->addChild('sources', [
                'label' => 'Sources',
                'route' => 'source_index',
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);
        }

        return $menu;
    }

    /**
     * Build a menu the footer.
     *
     * @return ItemInterface
     */
    public function footerMenu(array $options) {
        $menu = $this->factory->createItem('root');

        $menu->addChild('home', [
            'label' => 'Welcome',
            'route' => 'homepage',
        ]);

        $menu->addChild('about', [
            'label' => 'About',
            'route' => 'nines_blog_page_show',
            'routeParameters' => ['id' => 1],
        ]);

        $menu->addChild('clipping_index', [
            'label' => 'Archive',
            'route' => 'clipping_index',
        ]);

        $menu->addChild('clipping_search', [
            'label' => 'Search',
            'route' => 'clipping_search',
        ]);

        $menu->addChild('documentation', [
            'label' => 'Documentation',
            'uri' => $this->packages->getUrl('docs/sphinx/index.html'),
        ]);

        $menu->addChild('privacy', [
            'label' => 'Privacy',
            'uri' => 'https://docs.dhil.lib.sfu.ca/privacy.html',
            'linkAttributes' => [
                'target' => '_blank',
            ],
        ]);

        $menu->addChild('github', [
            'label' => 'Github',
            'uri' => 'https://github.com/sfu-dhil/circus',
        ]);

        return $menu;
    }
}

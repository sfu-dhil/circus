<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;
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

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Packages
     */
    private $packages;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authChecker, TokenStorageInterface $tokenStorage, Packages $packages) {
        $this->factory = $factory;
        $this->authChecker = $authChecker;
        $this->tokenStorage = $tokenStorage;
        $this->packages = $packages;
    }

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

        /* NOTE: Any changes made to main menu
        will not be reflected in the footer menu */

        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes([
                                         'class' => 'nav navbar-nav',
                                     ]);

        $menu->addChild('home', [
            'label' => 'Welcome',
            'route' => 'homepage',
        ]);

        $browse = $menu->addChild('browse', [
            'uri' => '#',
            'label' => 'Archive',
        ]);
        $browse->setAttribute('dropdown', true);
        $browse->setLinkAttribute('class', 'dropdown-toggle');
        $browse->setLinkAttribute('data-toggle', 'dropdown');
        $browse->setChildrenAttribute('class', 'dropdown-menu');

        $browse->addChild('astley1', [
            'label' => "Astley's Volume 1",
            'route' => 'source_show',
            'routeParameters' => ['id' => 1],
        ]);

        $browse->addChild('astley2', [
            'label' => "Astley's Volume 2",
            'route' => 'source_show',
            'routeParameters' => ['id' => 2],
        ]);

        $browse->addChild('astley3', [
            'label' => "Astley's Volume 3",
            'route' => 'source_show',
            'routeParameters' => ['id' => 3],
        ]);

        $browse->addChild('categories', [
            'label' => 'Categories',
            'route' => 'category_index',
        ]);

        $browse->addChild('all', [
            'label' => "View all",
            'route' => 'clipping_index',
        ]);

        if ($this->hasRole('ROLE_USER')) {
            $divider = $browse->addChild('divider', [
                'label' => '',
            ]);
            $divider->setAttributes([
                                        'role' => 'separator',
                                        'class' => 'divider',
                                    ]);

            $browse->addChild('categories', [
                'label' => 'Categories',
                'route' => 'category_index',
            ]);
            $browse->addChild('sources', [
                'label' => 'Sources',
                'route' => 'source_index',
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
            'route' => 'clipping_index'
        ]);

        $menu->addChild('clipping_search', [
            'label' => 'Search',
            'route' => 'clipping_search'
        ]);

        $menu->addChild('documentation', [
            'label' => 'Documentation',
            'uri' => $this->packages->getUrl('docs/sphinx/index.html')
        ]);

        $menu->addChild('privacy', [
            'label' => 'Privacy',
            'route' => 'privacy'
        ]);

        $menu->addChild('github', [
            'label' => 'Github',
            'uri' => 'https://github.com/sfu-dhil/circus'
        ]);


        return $menu;
    }
}

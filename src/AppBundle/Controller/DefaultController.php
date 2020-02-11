<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller {
    /**
     * Show the home page.
     *
     * @Route("/", name="homepage", methods={"GET"})
     * @Template()
     *
     * @return array
     */
    public function indexAction(Request $request) {
        return [];
    }

    /**
     * Show the privacy page.
     *
     * @Route("/privacy", name="privacy", methods={"GET"})
     * @Template()
     *
     * @return array
     */
    public function privacyAction(Request $request) {
        return [];
    }
}

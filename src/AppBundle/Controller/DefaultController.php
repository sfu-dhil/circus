<?php

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
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request) {
        return array();
    }

    /**
     * Show the privacy page.
     *
     * @Route("/privacy", name="privacy", methods={"GET"})
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function privacyAction(Request $request) {
        return array();
    }
}

<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * Show the home page.
     *
     * @Route("/", name="homepage", methods={"GET"})
     * @Template()
     * @return array
     */
    public function indexAction(Request $request)
    {
        return [];
    }

    /**
     * Show the privacy page.
     *
     * @Route("/privacy", name="privacy", methods={"GET"})
     * @Template()
     * @return array
     */
    public function privacyAction(Request $request) {
        return array();
    }
}

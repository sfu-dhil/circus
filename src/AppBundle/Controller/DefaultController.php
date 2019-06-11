<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return [];
    }

    /**
     * @Route("/privacy", name="privacy", methods={"GET"})
     * @Template()
     */
    public function privacyAction(Request $request) {

    }
}

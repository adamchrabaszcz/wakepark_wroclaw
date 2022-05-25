<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegulationsController extends AbstractController
{
    /**
     * @Route("/regulations", name="app_regulations")
     */
    public function index(): Response
    {
        return $this->render('regulations/index.html.twig', [
            'controller_name' => 'RegulationsController',
        ]);
    }
}

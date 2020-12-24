<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CastController extends AbstractController
{
    /**
     * @Route("/cast", name="cast")
     */
    public function index()
    {
        return $this->render('cast/index.html.twig', []);
    }
}

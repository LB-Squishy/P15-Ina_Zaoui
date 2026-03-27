<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class AboutController extends AbstractController
{
    #[Route("/about", name: "about")]
    public function about()
    {
        return $this->render('front/about.html.twig');
    }
}

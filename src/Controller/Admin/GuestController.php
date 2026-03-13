<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/admin/guest")]
final class GuestController extends AbstractController
{
    #[Route("", name: 'admin_guest_index')]
    public function index(): Response
    {
        return $this->render('admin/guest/index.html.twig', [
            'controller_name' => 'GuestController',
        ]);
    }
}

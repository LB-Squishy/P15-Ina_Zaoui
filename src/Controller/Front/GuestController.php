<?php

namespace App\Controller\Front;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class GuestController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    #[Route("/guests", name: "guests")]
    public function guests()
    {
        $guests = $this->userRepository->findAdminGuestsWithMediaCount();
        // dd($guests);
        return $this->render('front/guests.html.twig', [
            'guests' => $guests
        ]);
    }

    #[Route("/guest/{id}", name: "guest")]
    public function guest(int $id)
    {
        $guest = $this->userRepository->findOneBy(['id' => $id, 'super_admin' => false, 'admin' => true]);
        if (!$guest) {
            throw $this->createNotFoundException('Guest not found');
        }
        return $this->render('front/guest.html.twig', [
            'guest' => $guest
        ]);
    }
}

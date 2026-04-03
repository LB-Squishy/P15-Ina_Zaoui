<?php

namespace App\Controller\Front;

use App\Repository\UserRepository;
use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GuestController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private MediaRepository $mediaRepository
    ) {}

    #[Route("/guests", name: "guests")]
    public function guests(Request $request)
    {
        $page = $request->query->getInt('page', 1);

        $criteria = ['super_admin' => false, 'admin' => true];

        $guests = $this->userRepository->findBy(
            $criteria,
            ['id' => 'ASC'],
            5,
            5 * ($page - 1)
        );
        $total = $this->userRepository->count($criteria);

        return $this->render('front/guests.html.twig', [
            'guests' => $guests,
            'total' => $total,
            'page' => $page
        ]);
    }

    #[Route("/guest/{id}", name: "guest")]
    public function guest(int $id, Request $request)
    {
        $page = $request->query->getInt('page', 1);

        $criteria = ['id' => $id, 'super_admin' => false, 'admin' => true];

        $guest = $this->userRepository->findOneBy($criteria);
        if (!$guest) {
            throw $this->createNotFoundException('Guest not found');
        }

        $medias = $this->mediaRepository->findBy(
            ['user' => $guest],
            ['id' => 'ASC'],
            9,
            9 * ($page - 1)
        );
        $total = $this->mediaRepository->count(['user' => $guest]);

        return $this->render('front/guest.html.twig', [
            'guest' => $guest,
            'medias' => $medias,
            'total' => $total,
            'page' => $page
        ]);
    }
}

<?php

namespace App\Controller\Front;

use App\Repository\AlbumRepository;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class PortfolioController extends AbstractController
{
    public function __construct(
        private AlbumRepository $albumRepository,
        private MediaRepository $mediaRepository,
        private UserRepository $userRepository,
    ) {}

    #[Route("/portfolio/{id}", name: "portfolio")]
    public function portfolio(?int $id = null)
    {
        $albums = $this->albumRepository->findAll();
        $album = $id ? $this->albumRepository->find($id) : null;
        $user = $this->userRepository->findOneBy(['super_admin' => true]);

        $medias = $album
            ? $this->mediaRepository->findBy(['album' => $album])
            : $this->mediaRepository->findBy(['user' => $user]);
        return $this->render('front/portfolio.html.twig', [
            'albums' => $albums,
            'album' => $album,
            'medias' => $medias
        ]);
    }
}

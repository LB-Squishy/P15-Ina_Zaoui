<?php

namespace App\Controller\Front;

use App\Repository\AlbumRepository;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PortfolioController extends AbstractController
{
    public function __construct(
        private AlbumRepository $albumRepository,
        private MediaRepository $mediaRepository,
        private UserRepository $userRepository,
    ) {}

    #[Route("/portfolio/{id<\d+>?}", name: "portfolio")]
    public function portfolio(Request $request, ?int $id = null)
    {
        $albums = $this->albumRepository->findAll();
        $album = $id ? $this->albumRepository->find($id) : null;

        if ($id && !$album) {
            return $this->redirectToRoute('portfolio');
        }

        $page = $request->query->getInt('page', 1);

        $criteria = ['super_admin' => true];

        $user = $this->userRepository->findOneBy($criteria);

        if ($album) {
            $medias = $this->mediaRepository->findBy(['album' => $album], ['id' => 'ASC'], 6, 6 * ($page - 1));
            $total = $this->mediaRepository->count(['album' => $album]);
        } else {
            $medias = $this->mediaRepository->findBy(['user' => $user], ['id' => 'ASC'], 6, 6 * ($page - 1));
            $total = $this->mediaRepository->count(['user' => $user]);
        }

        return $this->render('front/portfolio.html.twig', [
            'albums' => $albums,
            'album' => $album,
            'medias' => $medias,
            'total' => $total,
            'page' => $page,
        ]);
    }
}

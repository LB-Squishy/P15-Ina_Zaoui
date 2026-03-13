<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\GuestType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/admin/guest")]
final class GuestController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    #[Route("", name: 'admin_guest_index')]
    public function index(Request $request)
    {
        $page = $request->query->getInt('page', 1);

        $criteria = ['super_admin' => false];

        $guests = $this->userRepository->findBy(
            $criteria,
            ['id' => 'ASC'],
            25,
            25 * ($page - 1)
        );
        $total = $this->userRepository->count($criteria);

        return $this->render('admin/guest/index.html.twig', [
            'guests' => $guests,
            'total' => $total,
            'page' => $page
        ]);
    }

    #[Route("/add", name: "admin_guest_add")]
    public function add(Request $request)
    {
        $guest = new User();
        $form = $this->createForm(GuestType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $guest->setPassword(password_hash($form->get('plainPassword')->getData(), PASSWORD_BCRYPT));
            $guest->setSuperAdmin(false);
            $this->entityManager->persist($guest);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_guest_index');
        }

        return $this->render('admin/guest/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/update/{id}", name: "admin_guest_update")]
    public function update(int $id)
    {
        $guest = $this->userRepository->find($id);
        $guest->setAdmin(!$guest->isAdmin());
        $this->entityManager->persist($guest);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_guest_index');
    }

    #[Route("/delete/{id}", name: "admin_guest_delete")]
    public function delete(int $id)
    {
        $guest = $this->userRepository->find($id);
        $this->entityManager->remove($guest);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_guest_index');
    }
}

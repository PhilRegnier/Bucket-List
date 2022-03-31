<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/wish', name: 'wish')]
class WishController extends AbstractController
{
    #[Route('/list', name: '_list')]
    public function liste(
        WishRepository $wishRepository
    ): Response
    {
        // $wishes = $wishRepository->findAll();
        $wishes = $wishRepository->findBy(
            ["isPublished" => true],
            ["dateCreated" => "DESC"]
        );
        return $this->render(
            'wish/list.html.twig',
            compact("wishes")
        );
    }
    #[Route('/detail{id}', name: '_detail', requirements: ['id' => '\d+'])]
    public function detail(
        Wish $wish
    ): Response
    {
        return $this->render(
            'wish/detail.html.twig',
            compact("wish"));
    }
    #[Route('/add', name: '_add')]
    public function ajouter(
        EntityManagerInterface $em,
        Request $request
    ): Response
    {
        $wish = new Wish();
        $wishForm = $this->createForm(WishType::class, $wish);

        $wishForm->handleRequest($request);
        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            $wish->setDateCreated(new DateTime());
            $wish->setIsPublished(true);
            $em->persist($wish);
            $em->flush();
            $this->addFlash('success','Be happy ! Your wish has been added');
            return $this->redirectToRoute('wish_list');
        }

        return $this->render(
            'wish/addition.html.twig',
            ['wishForm' => $wishForm->createView()]);
    }
}

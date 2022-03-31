<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Repository\WishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

}

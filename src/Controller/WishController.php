<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use App\Services\Censurator;
use App\Services\Courriel;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/wish', name: 'wish')]
class WishController extends AbstractController
{
    #[Route('/list', name: '_list')]
    public function liste(
        WishRepository $wishRepository,
        Censurator $censurator,
        Request $request
    ): Response
    {
        $filtreForm = $this->createFormBuilder()
            ->add('keywords',TextType::class,
                [
                    'label' => 'Type one word to see related wishes',
                    'required' => false
                ] )
            ->add('category', EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'required' => false
                ] )
            ->add('Search', SubmitType::class,
                ['label' => 'Filter'] )
            ->getForm();

        $filtreForm->handleRequest($request);

        if ($filtreForm->isSubmitted() && $filtreForm->isValid()
            && (!empty($filtreForm->get('keywords')->getData())
                || !empty($filtreForm->get('category')->getData())))
        {
            $wishes = $wishRepository->findByWithFilter(
                $filtreForm->get('keywords')->getData(),
                $filtreForm->get('category')->getData()
            );
        }
        else
        {
            $wishes = $wishRepository->findBy(
                ["isPublished" => true],
                ["dateCreated" => "DESC"]
            );
        }

        foreach ($wishes as $wish) {
            $wish->setTitle($censurator->purify($wish->getTitle()));
            $wish->setDescription($censurator->purify($wish->getDescription()));
        }
        return $this->render(
            'wish/list.html.twig',
            [
                'filtreForm' => $filtreForm->createView(),
                'wishes' => $wishes
            ]
        );
    }
    #[Route('/detail{id}', name: '_detail', requirements: ['id' => '\d+'])]
    public function detail(
        Wish $wish,
        Censurator $censurator
    ): Response
    {
        $wish->setTitle($censurator->purify($wish->getTitle()));
        $wish->setDescription($censurator->purify($wish->getDescription()));
        return $this->render(
            'wish/detail.html.twig',
            compact("wish"));
    }
    #[Route('/add', name: '_add')]
    #[IsGranted('ROLE_USER')]
    public function ajouter(
        EntityManagerInterface $em,
        Courriel $courriel,
        Request $request
    ): Response
    {
        $wish = new Wish();
        $wishForm = $this->createForm(WishType::class, $wish);

        $wishForm->handleRequest($request);
        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            $wish->setAuthor($this->getUser()->getUsername());
            $wish->setDateCreated(new DateTime());
            $wish->setIsPublished(true);
            $em->persist($wish);
            $em->flush();

            $courriel->envoi();

            $this->addFlash('success','Be happy ! Your wish has been added');
            return $this->redirectToRoute('wish_detail', ["id" => $wish->getId()]);
        }

        return $this->render(
            'wish/addition.html.twig',
            ['wishForm' => $wishForm->createView()]);
    }
}

<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class MainController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    #[Route('/', name: "main_home")]
    public function accueil(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('main/home.html.twig');
    }
    #[Route('/about_us', name: "main_aboutUs")]
    public function aboutUs(): \Symfony\Component\HttpFoundation\Response
    {
        $str_json = file_get_contents('../data/team.json');
        $membres = json_decode($str_json,true);

        return $this->render('main/about_us.html.twig',
            compact('membres'));
    }

}
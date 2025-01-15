<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Security $security): Response
    {
        $user = $security->getUser();

        if($user === null)
        {
            return $this->render('home/index.html.twig');
        }
         $username = $user->getUsername();
        return $this->render('home/index.html.twig', [
            'username' => $username,
        ]);
    }
}

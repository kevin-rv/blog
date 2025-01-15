<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/sign-up', name: 'sign_up')]
    public function sign(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, Security $security): Response
    {
        if (empty($security->getUser())) {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $password = $user->getPassword();
    
                $hash = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hash)
                    ->setRoles(['ROLE_USER']);
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('app_login');
            }
            return $this->render('user/sign_up.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        $this->addFlash("danger", "veuillez vous deconnecter pour creer un nouveau compte");
        return $this->redirectToRoute("article");

    }
}

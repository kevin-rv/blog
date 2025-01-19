<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/sign-up', name: 'sign_up')]
    public function sign(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, Security $security, UserRepository $userRepository): Response
    {
    if ($security->getUser()) {
        $this->addFlash("danger", "Veuillez vous déconnecter pour créer un nouveau compte.");
        return $this->redirectToRoute("article");
    }
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);
    if ($form->isSubmitted()) {
        if ($userRepository->findOneBy(['username' => $user->getUsername()])) {
            $form->get('username')->addError(new FormError('Ce nom d\'utilisateur est déjà pris.'));
        }

        if ($form->isValid() && !$form->get('username')->getErrors()->count()) {
            $password = $user->getPassword();
            $hash = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hash)
                ->setRoles(['ROLE_USER']);  
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Inscription réussie !');

            return $this->redirectToRoute('app_login');
        }
    }
    return $this->render('user/sign_up.html.twig', [
        'form' => $form->createView(),
    ]);
}
}

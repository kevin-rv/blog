<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function create(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $contact = new Contact;
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             $em->persist($contact);
             $em->flush();
            
            $email = (new Email())
            ->from($_ENV['MAILER_FROM_ADDRESS']) // Utiliser la variable d'environnement
            ->to($_ENV['MAILER_FROM_ADDRESS']) // Utiliser la variable d'environnement
            ->subject('Nouveau message de contact')
            ->text("Vous avez reçu un nouveau message de : \n" . $contact->getEmail() . "\n\nmessage: " .  $contact->getMessage());

            
            try {
                $mailer->send($email);
            } catch (\Exception $e) {
                echo 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage();
            }
           $this->addFlash("success", "Message envoyer");
           return $this->redirectToRoute("article");
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contactAll', name: 'all_contact')]
    public function getAllContact(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();
        
        return $this->render('contact/getAll.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    #[Route('/contact/{contactId}', name: 'show_contact')]
    public function getOneContact(int $contactId, ContactRepository $contactRepository): Response
    {
        $contact = $contactRepository->find($contactId);
        
        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/contact/{contactId}/delete', name: 'delete_contact')]
    public function delete(int $contactId, ContactRepository $contactRepository, EntityManagerInterface $em): Response
    {
        $contact = $contactRepository->find($contactId);
        $em->remove($contact);
        $em->flush();
        
        $this->addFlash("success", "suppression reussi");
        return $this->redirectToRoute("all_contact");
    }
}

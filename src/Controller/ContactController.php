<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function sendEmail(Request $request,EntityManagerInterface $manager,MailerInterface $mailer): Response {

        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $manager->persist($contact);
            $manager->flush();
            
            
            $email = (new Email())
            ->from($contact->getEmail())
            ->to('julien.boisgard37@gmail.com')
            ->subject('Nom :'.$contact->getFirstname() .' Prenom: '. $contact->getLastname() .' Email: '.$contact->getEmail()  )
            ->text($contact->getMessage());

            $mailer->send($email);
        
            

            $this->addFlash('success', 'Vore message a été envoyé');
            return $this->redirectToRoute('app_contact');

        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
}
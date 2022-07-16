<?php

namespace App\Controller;

use App\Entity\Newsletter\Users;
use App\Form\NewslettersType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/newsletters', name: 'newsletters_')]
class NewslettersController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $user = new Users();

        $form = $this->createForm(NewslettersType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = md5(uniqid());

            $user->setValidationToken($token);

            $em->persist($user);
            $em->flush();

            $email = (new TemplatedEmail())
            ->from("Newsletter@site.fr")
            ->to($user->getEmail())
            ->subject("Votre inscription a notre newsletter")
            ->htmlTemplate('emails/inscription.html.twig')
            ->context([
                'user'=>$user,
                'token'=>$token
            ])
            ;

            $mailer->send($email);

            $this->addFlash(
                'message',
                'Inscription en attente'
            );

            return $this->redirectToRoute('main');
        }

        return $this->render('newsletters/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/confirm/{id}/{token}', name: 'confrimation')]
    public function confirm(Users $user, EntityManagerInterface $em, $token): Response
    {
        if ($user->getValidationToken() != $token) {
            throw $this->createNotFoundException('Page non trouver');
        }

        $user->setIsValid(true);

        $em->persist($user);
        $em->flush();

        $this->addFlash(
            'message',
            'Compte activer'
        );

        return $this->redirectToRoute('main');
    }

}

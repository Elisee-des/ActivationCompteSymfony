<?php

namespace App\Controller;

use App\Entity\Newsletter\Newsletter;
use App\Entity\Newsletter\Users;
use App\Form\NewslettersUsers;
use App\Form\NewslettersUsersType;
use App\Form\NewsletterType;
use App\Repository\Newsletter\NewsletterRepository;
use App\Services\NewsletterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/newsletters', name: 'newsletters_')]
class NewslettersUsersController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $user = new Users();

        $form = $this->createForm(NewslettersUsersType::class, $user);

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
                    'user' => $user,
                    'token' => $token
                ]);

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

    #[Route('/prepare', name: 'prepare')]
    public function prepare(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $newsletter = new Newsletter();

        $form = $this->createForm(NewsletterType::class, $newsletter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($newsletter);
            $em->flush();

            $this->addFlash(
                'message',
                'Inscription en attente'
            );

            return $this->redirectToRoute('newsletters_list');
        }

        return $this->render('newsletters/prepare.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/list', name: 'list')]
    public function liste(NewsletterRepository $newsletterRepository, EntityManagerInterface $em, MailerInterface $mailer): Response
    {

        return $this->render('newsletters/list.html.twig', [
            'newsletters' => $newsletterRepository->findAll()
        ]);
    }

    #[Route('/send/{id}', name: 'send')]
    public function send(Newsletter $newsletter, EntityManagerInterface $em, NewsletterService $newsletterService): Response
    {
        $users = $newsletter->getCategories()->getUsers();

        foreach ($users as $user) {
            sleep(4);
            if ($user->getIsValid() == true) {
                $newsletterService->send($user, $newsletter);
            }

            // $newsletter->setIsSend(true);
            // $em->persist($newsletter);
            // $em->flush();
        }

        return $this->redirectToRoute('newsletters_list');
    }

    #[Route('/unsubscriber/{id}/{newsletter}/{token}', name: 'unsubscriber')]
    public function unsubcriber(Users $user, Newsletter $newsletter, EntityManagerInterface $em, $token): Response
    {
        if($user->getValidationToken() != $token)
        {
            throw $this->createNotFoundException('Page non trouver');
        }

        if(count($user->getCategories()) > 1)
        {
            $user->removeCategory($newsletter->getCategories());
            $em->persist($user);
        }else{
            $em->remove($user);
        }

        $em->flush();

        $this->addFlash(
           'message',
           'Newsletter supprimer'
        );

        return $this->redirectToRoute('main');
    }
}

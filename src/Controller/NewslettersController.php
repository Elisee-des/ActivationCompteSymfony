<?php

namespace App\Controller;

use App\Entity\Newsletter\Users;
use App\Form\NewslettersType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/newsletters', name: 'newsletters_')]
class NewslettersController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $user = new Users();

        $form = $this->createForm(NewslettersType::class, $user);

        return $this->render('newsletters/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

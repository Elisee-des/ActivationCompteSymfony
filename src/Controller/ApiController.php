<?php

namespace App\Controller;

use App\Entity\Calendar;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    #[Route('/api/{id}/edit', name: 'api_event_edit', methods: 'PUT')]
    public function editEvent(?Calendar $calendar, Request $request, EntityManagerInterface $em): Response
    {
        //on recupere les donnees
        $donnee = json_decode($request->getContent());

        if(
            isset($donnee->title) && !empty($donnee->title) &&
            isset($donnee->start) && !empty($donnee->start) &&
            isset($donnee->description) && !empty($donnee->description) &&
            isset($donnee->backgroundColor) && !empty($donnee->backgroundColor) &&
            isset($donnee->borderColor) && !empty($donnee->borderColor) &&
            isset($donnee->textColor) && !empty($donnee->textColor)
        )
        {
            //les donnees sont complet
            $code = 200;

            //si le id exciste
            if(!$calendar)
            {
                //on instancie un rendez-vs
                $calendar = new Calendar();

                //on change le code
                $code = 201;
            }

            //on hydrate l'object avec les donnees
            $calendar->setTitle($donnee->title);
            $calendar->setDescription($donnee->description);
            $calendar->setStart(new DateTime($donnee->title));
            if($donnee->allDay)
            {
                $calendar->setEnd(new DateTime($donnee->start));
            }else{
                $calendar->setEnd(new DateTime($donnee->end));
            }
            $calendar->setAllDay($donnee->allDay);
            $calendar->setBackgroundColor($donnee->backgroundColor);
            $calendar->setBorderColor($donnee->borderColor);
            $calendar->setTextColor($donnee->textColor);

            $em->persist($calendar);
            $em->flush();

            //on retour un code
            return new Response("ok", $code);

        }else {
            //les donner sont incomplet
            return new Response('Donnee incomplet', 404);
        }
        

        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
}

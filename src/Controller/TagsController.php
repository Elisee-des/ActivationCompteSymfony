<?php

namespace App\Controller;

use App\Entity\Tags;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Label;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagsController extends AbstractController
{
    #[Route('/tags/ajout/ajax/{label}', name: 'tags_add_ajax', methods: 'POST')]
    public function index(string $label, EntityManagerInterface $em): Response
    {
        $tag = new Tags();
        $tag->setName(trim(strip_tags($label)));
        $em->persist($tag);
        $em->flush();

        $id = $tag->getId();

        return new JsonResponse(["id"=>$id]);
    }
}

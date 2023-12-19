<?php

namespace App\Controller;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        $projets = $em->getRepository(Project::class)->findAll();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'projets' => $projets
        ]);
    }
}

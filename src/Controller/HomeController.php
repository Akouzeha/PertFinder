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
        
        // Array to store image names and corresponding diagram titles
        $imgData = [];

        foreach ($projets as $projet) {
            if (count($projet->getDiagrams()) == 0) {
                continue;
            }

            $diagram = $projet->getDiagrams()[0];
            $taskNumber = count($diagram->getTasks());
            $imgName = $diagram->getImgName();
            $diagramTitle = $diagram->getTitle();
            $projectId = $projet->getId();

            // Store image name and diagram title in the array
            $imgData[] = ['imgName' => $imgName, 'diagramTitle' => $diagramTitle, 'projectId' => $projectId, 'taskNumber' => $taskNumber];
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'projets' => $projets,
            'imgData' => $imgData,
        ]);
    }

}

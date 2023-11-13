<?php

namespace App\Controller;

use App\Entity\Diagram;
use App\Entity\Project;
use App\Form\ChartType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DiagramController extends AbstractController
{
    #[Route('/diagram', name: 'app_diagram')]
    public function index(): Response
    {
        return $this->render('diagram/index.html.twig', [
            'controller_name' => 'DiagramController',
        ]);
    }

    #[Route('/diagram/{projectId}/new', name: 'create_diagram')]
    public function createDiagram($projectId, Request $request, Diagram $diagram = NULL, EntityManagerInterface $em): Response
    {
        if(!$diagram){
            $diagram = new Diagram();
        }
        $project = $em->getRepository(Project::class)->find($projectId);
        $user = $this->getUser();
        $diagram->setCreatedAt(new \DateTime());
        $form = $this->createForm(ChartType::class, $diagram);
        $form->handleRequest($request);
        $showForm = true;
        if($form->isSubmitted() && $form->isValid()){
            $diagram = $form->getData();
            $diagram->setProject($project);
            $diagram->setUser($user);
            $em->persist($diagram);
            $em->flush();
            $showForm = false;
            return $this->redirectToRoute('task/index.html.twig');
        }
        return $this->render('diagram/create.html.twig', [
            'formDiagram' => $form,
            'showForm' => $showForm,
            'projectId' => $projectId,
        ]);
        
    }
}

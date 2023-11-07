<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjectController extends AbstractController
{
    #[Route('/project', name: 'app_project')]
    public function index(): Response
    {
        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
        ]);
    }
    /**
     * @Route("/project/create", name="project_show")
     * @return Response
     * create a new project
     */
    #[Route('/project/{id}/edit', name: 'edit_project')]
    #[Route('/project/create', name: 'create_project')]
    public function createProject(Project $project = Null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$project){
            $project = new Project();
        }
        $form = $this->createForm(ProjetType::class, $project);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $project = $form->getData();
            $entityManager->persist($project);
            $entityManager->flush();
            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }
        return $this->render('project/create.html.twig', [
            'formProject' => $form,
        ]);
    }
    
}

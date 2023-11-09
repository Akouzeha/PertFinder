<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjetType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjectController extends AbstractController
{
    #[Route('/project', name: 'app_project')]
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();
        return $this->render('project/index.html.twig', [
            'projects' => $projects,
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
        $user = $this->getUser();
        if(!$project){
            $project = new Project();
        }
        $project->setUser($user);
        $form = $this->createForm(ProjetType::class, $project);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $project = $form->getData();
            $entityManager->persist($project);
            $entityManager->flush();
            //return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
            return $this->redirectToRoute('app_project');
        }
        return $this->render('project/create.html.twig', [
            'formProject' => $form,
        ]);
    }

    /**
     * @Route("/project/{id}", name="delete_project")
     * @return Response
     * delete a project
     */

    #[Route('/project/{id}', name: 'delete_project')]
    public function deleteProject(Project $project, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($project);
        $entityManager->flush();
        return $this->redirectToRoute('app_project');
    }
}

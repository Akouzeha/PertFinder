<?php

namespace App\Controller;

use App\Entity\Diagram;
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
        $project->setCreatedAt(new \DateTime());
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

    #[Route('/project/project/{id}', name: 'show_project')]
    public function showProject(Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($project->getDiagrams() == null) {
            $imgName = 'default.png';
        } else {
            $diagrams = $project->getDiagrams();
            if ($diagrams[0] != null) {
                $diagramName = $diagrams[0]->getTitle();
                $imgName = str_replace(' ', '_', $diagramName);
            } else {
                $imgName = 'default.png';
            }
        }
        
        return $this->render('project/show.html.twig',[
            'project' => $project,
            'imgName' => $imgName . '.png',
        ]);
    }

}

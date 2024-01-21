<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Diagram;
use App\Entity\Project;
use App\Form\ProjetType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ProjectController extends AbstractController
{
    #[Route('/project', name: 'app_project')]
    public function index(ProjectRepository $projectRepository, CommentRepository $commentRepository): Response
    {
        
        $projects = $projectRepository->findAll();

        $commentCounts = [];
        foreach ($projects as $project) {
            $commentCounts[$project->getId()] = $commentRepository->countCommentsInProject($project->getId());
        }

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
            'commentCounts' => $commentCounts,
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
        if($project->getId() != null && ($project->getUser() != $user or !$this->isGranted('ROLE_PROJECT_MANAGER'))){
            $this->addFlash('error', 'Vous n\'avez pas le droit de modifier ce projet');
            return $this->redirectToRoute('app_project');
        }
        $project->setUser($user);
        $project->setCreatedAt(new \DateTime());
        $project->setIsLocked(false);
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

    #[Route('/project/show/{id}', name: 'show_project')]
    public function showProject(Project $project, EntityManagerInterface $em,Request $request, Comment $comment = NULL): Response
    {
        if ($project->getDiagrams() == null) {
            $imgName = 'default.png';
        } else {
            $diagrams = $project->getDiagrams();
            if ($diagrams[0] != null) {
                $diagramName = $diagrams[0]->getTitle();
                $diagramId = $diagrams[0]->getId();
                $imgName = str_replace(' ', '_', $diagramName);
                
            } else {
                $imgName = 'default.png';
            }
        }
        $projectId = $project->getId();
        //add a comment to the project
        $user = $this->getUser();
        if(!$comment){
            $comment = new Comment();
        }
        $commentId = $comment->getId();
        $comment->setProject($project);
        $comment->setUser($user);
        $comment->setCommentTime(new \DateTime());
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            
            $comment = $form->getData();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('show_project', ['id' => $projectId]);
        }
        $projectDuree = $project->calculNumberDays();
        return $this->render('project/show.html.twig',[
            'project' => $project,
            'commentId' => $commentId,
            'projectDuree' => $projectDuree,
            'imgName' => $imgName . '.png',
            'formComment' => $form,
        ]);
    }
    

    /**
     * @Route("/project/show/{commentId}", name="edit_comment")
     * @return Response
     * edit a comment
     */
    #[Route('/project/show/{id}/{commentId}', name: 'edit_comment')]
    public function editComment($commentId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = $entityManager->getRepository(Comment::class)->find($commentId);
        $user = $comment->getUser();
        if ($user != $this->getUser()) {
            throw new AccessDeniedHttpException('Vous n\'avez pas le droit de modifier ce commentaire');
        }
        $projectId = $comment->getProject()->getId();
        $project = $entityManager->getRepository(Project::class)->find($projectId);
        if ($project->getDiagrams() == null) {
            $imgName = 'default.png';
        } else {
            $diagrams = $project->getDiagrams();
            if ($diagrams[0] != null) {
                $diagramName = $diagrams[0]->getTitle();
                $diagramId = $diagrams[0]->getId();
                $imgName = str_replace(' ', '_', $diagramName);
            } else {
                $imgName = 'default.png';
            }
        }
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('show_project', ['id' => $projectId]);
        }
        return $this->render('project/show.html.twig', [
            'formEditComment' => $form,
            'project' => $project,
            'imgName' => $imgName . '.png',
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

     #[Route('/project/{id}/lock', name: 'lock_project')]
     public function lockProject(EntityManagerInterface $em, $id): Response
     {
         
        $project = $em->getRepository(Project::class)->find($id);
       //verify if the user has the right to lock the project
        if ($project->getUser() == $this->getUser() || $this->isGranted('ROLE_MODERATOR')) {
            //verify if the project is locked or not
            if ($project->isIsLocked() == true) {
                $project->setIsLocked(false);
            } else {
                $project->setIsLocked(true);
            }
            $em->persist($project);
            $em->flush();
        }
        return $this->redirectToRoute('show_project', ['id' => $id]);

     }

}

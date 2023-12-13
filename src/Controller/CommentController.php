<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Project;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }
    #[Route('/project/{projectId}/{userId}/newcomment', name: 'new_comment')]
    public function newComment(EntityManagerInterface $em, $projectId, $userId, Request $request, Comment $comment = null): Response
    {
        $project = $em->getRepository(Project::class)->find($projectId);
        $user = $em->getRepository(User::class)->find($userId);
        $time = new \DateTime();

        // Check if $comment is null to determine if it's a new comment or an edit
        if (!$comment) {
            $comment = new Comment();
        }
        $comment->setProject($project);
        $comment->setUser($user);
        $comment->setCommentTime($time);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('show_project', ['id' => $projectId]);
        }
        $formComment = $form->createView();

        return $this->render('project/show.html.twig', [
            'formComment' => $formComment, // Pass the form to the template,
            'project' => $project,
            'user' => $user,
            'comment' => $comment, // Pass the comment to the template for rendering/editing
        ]);
    }

    #[Route('/project/{commentId}/delcomment', name: 'del_comment')]
    public function delComment(EntityManagerInterface $em, $commentId): Response
    {
        
        $projectId = $em->getRepository(Comment::class)->find($commentId)->getProject()->getId();    
        $comment = $em->getRepository(Comment::class)->find($commentId);
            $em->remove($comment);
            $em->flush();
            return $this->redirectToRoute('show_project', ['id' => $projectId]);
        
    }

}

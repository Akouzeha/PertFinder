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

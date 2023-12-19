<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,
        ]);
    }

    #[Route('/admin/promote/{userId}', name: 'promote_user')]
    public function promoteUser(EntityManagerInterface $em, $userId): Response
    {
        $user = $em->getRepository(User::class)->find($userId);
        if($this->isGranted('ROLE_ADMIN')) {
            if($user->getRoles() == ['ROLE_USER']) {
                $user->setRoles(['ROLE_MODERATOR']);
            } else if($user->getRoles() == ['ROLE_MODERATOR']) {
                $user->setRoles(['ROLE_ADMIN']);
            }
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'User a été promu avec succès');
            return $this->redirectToRoute('app_admin');
        }
        
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            
        ]);
    }

    #[Route('/admin/demote/{userId}', name: 'demote_user')]
    public function demoteUser(EntityManagerInterface $em, $userId): Response
    {
        
        $user = $em->getRepository(User::class)->find($userId);

        if($this->isGranted('ROLE_ADMIN')) {
            if($user->getRoles() == ['ROLE_SUPER_ADMIN']) {
                $this->addFlash('error', 'Vous ne pouvez pas rétrograder un super admin');
                return $this->redirectToRoute('app_admin');
                } else  {
                $user->setRoles(['ROLE_USER']);
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'User a été rétrogradé avec succès');
                return $this->redirectToRoute('app_admin');

            }
        }
        
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            
        ]);
    }


}

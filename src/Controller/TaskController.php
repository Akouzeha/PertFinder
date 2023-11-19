<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Entity\Diagram;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    #[Route('/task/{diagramId}/new', name: 'app_task')]
    public function index(Request $request, EntityManagerInterface $em, Task $task = NULL, $diagramId): Response
    {
        if(!$task){
            $task = new Task();
        }
        $diagram = $em->getRepository(Diagram::class)->find($diagramId);
        $task->setPertChart($diagram);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        $showForm = true;
        if($form->isSubmitted() && $form->isValid()){
            $task = $form->getData();
            $em->persist($task);
            $em->flush();
            $showForm = false;
            return $this->redirectToRoute('task/index.html.twig');
        }
        return $this->render('task/index.html.twig', [
            'formTask' => $form,
            'showForm' => $showForm,
            'diagramId' => $diagramId,
        ]);

    }

}

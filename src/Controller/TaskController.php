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
    
    #[Route('/task', name: 'app_task')]
    public function index(EntityManagerInterface $em): Response
    {
        $tasks = $em->getRepository(Task::class)->findAll();
        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
    
    
    
    #[Route('/task/{diagramId}/new', name: 'new_task')]
    public function newTask(Request $request, EntityManagerInterface $em, Task $task = NULL, $diagramId): Response
    {
        if(!$task){
            $task = new Task();
        }
        $diagram = $em->getRepository(Diagram::class)->find($diagramId);
        //associate the task to the chart
        $task->setPertChart($diagram);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        $showForm = true;
        if($form->isSubmitted() && $form->isValid()){
            $task = $form->getData();
            //verify is the name is unique
            $taskName = $task->getName();
            $taskExist = $em->getRepository(Task::class)->findOneBy(['name' => $taskName]);
            if($taskExist){
                $this->addFlash('error', 'Ce nom de tâche existe déjà');
                return $this->redirectToRoute('new_task', ['diagramId' => $diagramId]);
            }
            //verify if the data is valid
            if($task->getPesTime() <= $task->getMosTime()){
                $this->addFlash('error', 'Le temps pessimiste doit être supérieur ou égal au temps le plus probable');
                return $this->redirectToRoute('new_task', ['diagramId' => $diagramId]);
            }
            if($task->getMosTime() <= $task->getOptTime()){
                $this->addFlash('error', 'Le temps optimiste doit être inférieur ou égal au temps le plus probable');
                return $this->redirectToRoute('new_task', ['diagramId' => $diagramId]);
            }
            //calculate the duration 
            $duration = ($task->getOptTime() + 4 * $task->getMosTime() + $task->getPesTime())/6;
            $task->setDuree($duration);
            //calculate the variance
            $variance_format = pow(($task->getPesTime() - $task->getOptTime()), 2) / 36;
            $variance = round($variance_format, 3);
            $task->setVariance($variance);
            $em->persist($task);
            $em->flush();
            $this->addFlash('success', 'Tâche ajoutée avec succès');

            return $this->redirectToRoute('new_task', ['diagramId' => $diagramId]);
        }
        $tasks = $em->getRepository(Task::class)->findBy(['pertChart' => $diagramId]);
        return $this->render('task/index.html.twig', [
            'formTask' => $form,
            'showForm' => $showForm,
            'diagramId' => $diagramId,
            'tasks' => $tasks,
        ]);
    }

    //delete a task
    #[Route('/task/{id}', name: 'delete_task')]
    public function deleteTask(Task $task, EntityManagerInterface $em): Response
    {
        $em->remove($task);
        $em->flush();
        $this->addFlash('success', 'Tâche supprimée avec succès');
        return $this->redirectToRoute('new_task', ['diagramId' => $task->getPertChart()->getId()]);
    }

    #[Route('/task/{id}/{chartId}/edit', name: 'edit_task')]
    public function editTask(Request $request, EntityManagerInterface $em,Task $task, $chartId): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $task = $form->getData();
            //verify if the data is valid
            if($task->getPesTime() <= $task->getMosTime()){
                $this->addFlash('error', 'Le temps pessimiste doit être supérieur ou égal au temps le plus probable');
                return $this->redirectToRoute('edit_task', ['id' => $task->getId(), 'chartId' => $chartId]);
            }
            if($task->getMosTime() <= $task->getOptTime()){
                $this->addFlash('error', 'Le temps optimiste doit être inférieur ou égal au temps le plus probable');
                return $this->redirectToRoute('edit_task', ['id' => $task->getId(), 'chartId' => $chartId]);
            }
            //calculate the duration 
            $duration = ($task->getOptTime() + 4 * $task->getMosTime() + $task->getPesTime())/6;
            $task->setDuree($duration);
            //calculate the variance
            $variance_format = pow(($task->getPesTime() - $task->getOptTime()), 2) / 36;
            $variance = round($variance_format, 3);
            $task->setVariance($variance);
            $em->persist($task);
            $em->flush();
            $this->addFlash('success', 'Tâche modifiée avec succès');
            return $this->redirectToRoute('new_task', ['diagramId' => $chartId]);
        }
        return $this->render('task/edit.html.twig', [
            'formTask' => $form,
            'task' => $task,
            'chartId' => $chartId,
        ]);

    }

    #[Route('/task/{taskId}/dep', name: 'add_dep')]
    public function addDep(Request $request, EntityManagerInterface $em, $taskId): Response
    {
        $task = $em->getRepository(Task::class)->findOneBy(['id' => $taskId]);
        if(!$task){
            $this->addFlash('error', 'Cette tâche n\'existe pas');
            return $this->redirectToRoute('new_task', ['diagramId' => $task->getPertChart()->getId()]);
        }
        if($request->isMethod('POST')){
            // Get the dependent task name from the form
            $depTaskId = $request->request->get('dependent_task');
            //find the task by name
            $depTask = $em->getRepository(Task::class)->findOneBy(['id' => $depTaskId]);

            if(!$depTask){
                $this->addFlash('error', 'Cette tâche n\'existe pas');
                return $this->redirectToRoute('new_task', ['diagramId' => $task->getPertChart()->getId()]);
            }
            else if ($task->getDependentTasks()->contains($depTask)) {
                // Handle the case of duplicate dependency
                $this->addFlash('error', 'Dépendance déjà existante');
                return $this->redirectToRoute('new_task', ['diagramId' => $task->getPertChart()->getId()]);
            }
            else if($depTask->getDependentTasks()->contains($task)){
                // Handle the case of circular dependency
                $this->addFlash('error', 'Dépendance circulaire');
                return $this->redirectToRoute('new_task', ['diagramId' => $task->getPertChart()->getId()]);
            }
            else if($depTask->getId() === $task->getId()){
                // Handle the case of self dependency
                $this->addFlash('error', 'Une tâche ne peut pas dépendre d\'elle-même');
                return $this->redirectToRoute('new_task', ['diagramId' => $task->getPertChart()->getId()]);
            }
            else{
                // Add the dependent task to the dependencies array
                $task->addDependentTask($depTask);
                $em->persist($task);
                $em->flush();
            }
            
        }
        return $this->redirectToRoute('new_task', ['diagramId' => $task->getPertChart()->getId()]);
    }

    #[Route('/task/{taskId}/dep/{depTaskId}/delete', name: 'delete_dep')]
    public function deleteDep(EntityManagerInterface $em, $taskId, $depTaskId): Response
    {
        $task = $em->getRepository(Task::class)->findOneBy(['id' => $taskId]);
        $depTask = $em->getRepository(Task::class)->findOneBy(['id' => $depTaskId]);
        if(!$task || !$depTask){
            $this->addFlash('error', 'Cette tâche n\'existe pas');
            return $this->redirectToRoute('new_task', ['diagramId' => $task->getPertChart()->getId()]);
        }
        $task->removeDependentTask($depTask);
        $em->persist($task);
        $em->flush();
        return $this->redirectToRoute('new_task', ['diagramId' => $task->getPertChart()->getId()]);
    }
}

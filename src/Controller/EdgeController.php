<?php

namespace App\Controller;

use App\Entity\Edge;
use App\Entity\Task;
use App\Entity\Diagram;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EdgeController extends AbstractController
{
    #[Route('/edge', name: 'app_edge')]
    public function index(): Response
    {
        return $this->render('edge/index.html.twig', [
            'controller_name' => 'EdgeController',
        ]);
    }
    /*  #[Route('/edge/show', name: 'task_edge')]
    public function tasksWithDependencies(EntityManagerInterface $em): Response
    {
        // Fetch all tasks
        $tasks = $em->getRepository(Task::class)->findAll();
        //fetch all edges
        $edges = $em->getRepository(Edge::class)->findAll();

        // Create a mapping between task IDs and array indices
        $taskIndices = [];
        foreach ($tasks as $index => $task) {
            $taskIndices[$task->getId()] = $index;
        }
        // Create an empty adjacency matrix
        
        $numberOfTasks = count($tasks);
        $adjacencyMatrix = array_fill(0, $numberOfTasks, array_fill(0, $numberOfTasks, 0));

        // Populate the adjacency matrix based on edges
        foreach ($edges as $edge) {
        $taskIndex = $taskIndices[$edge->getTask()->getId()];
        $predecessorIndex = $taskIndices[$edge->getPredecessor()->getId()];

        // Assuming a directed graph, set the adjacency matrix value to 1
        $adjacencyMatrix[$taskIndex][$predecessorIndex] = 1;
        }

        // Convert the adjacency matrix to a simple one-dimensional array for each task
        foreach ($adjacencyMatrix as $key => $matrixRow) {
            $adjacencyMatrix[$key] = array_values($matrixRow);
        }

        // Combine task data with pertChart IDs and the adjacency matrix
        $taskData = []; 
        foreach ($tasks as $task) {
            $edges = $em->getRepository(Edge::class)->findBy(['task' => $task]);
            $predecessor = $em->getRepository(Edge::class)->findBy(['predecessor' => $task]);

            $taskData[] = [
                'task' => $task,
                'diagramId' => $task->getPertChart()->getId(),
                'edges' => $edges,
                'predecessors' => $predecessor,
                'adjacencyMatrix' => $adjacencyMatrix[$taskIndices[$task->getId()]],
            ];
        }

        return $this->render('edge/show.html.twig', [
            'taskData' => $taskData,
        ]);
    } */

    #[Route('/edge/{diagramId}/show', name: 'task_edge')]
    public function tasksWithDependencies(EntityManagerInterface $em, Request $request): Response
    {
        $diagramId = $request->get('diagramId');
        
        // Fetch all tasks of this diagram
        $tasks = $em->getRepository(Task::class)->findBy(['pertChart' => $diagramId]);
        //fetch all edges of this diagram
        $edges = $em->getRepository(Edge::class)->findAllEdgesForChart($diagramId);

        // Create a mapping between task IDs and array indices
        $taskIndices = [];
        foreach ($tasks as $index => $task) {
            $taskIndices[$task->getId()] = $index;
        }

        // Create an empty adjacency matrix
        $numberOfTasks = count($tasks);
        $adjacencyMatrix = array_fill(0, $numberOfTasks, array_fill(0, $numberOfTasks, 0));

        // Populate the adjacency matrix based on edges
        foreach ($edges as $edge) {
            $taskIndex = $taskIndices[$edge->getTask()->getId()];
            $predecessorIndex = $taskIndices[$edge->getPredecessor()->getId()];

            // Assuming a directed graph, set the adjacency matrix value to 1
            $adjacencyMatrix[$taskIndex][$predecessorIndex] = 1;
        }

        // Convert the adjacency matrix to a simple one-dimensional array for each task
        foreach ($adjacencyMatrix as $key => $matrixRow) {
            $adjacencyMatrix[$key] = array_values($matrixRow);
        }

        // Calculate levels
        $this->calculateAndSaveLevels($tasks, $adjacencyMatrix, $em);

        // Fetch the updated tasks from the database
        $tasks = $em->getRepository(Task::class)->findby(['pertChart' => $diagramId]);

        $pertChartData = [];
        foreach ($tasks as $task) {
            $pertChartData[] = [
                'id' => $task->getId(),
                'duration' => $task->getDuree(),
                'name' => $task->getName(),
            ];
        }
        $pertChartDataEdges = [];
        foreach ($edges as $edge) {
            $pertChartDataEdges[] = [
                'from' => $edge->getPredecessor()->getId(),
                'to' => $edge->getTask()->getId(),
            ];
        }

        // Combine task data with pertChart IDs, the adjacency matrix, and levels
        $taskData = [];
        foreach ($tasks as $task) {
            $edges = $em->getRepository(Edge::class)->findBy(['task' => $task]);
            $predecessor = $em->getRepository(Edge::class)->findBy(['predecessor' => $task]);
            $diagram = $em->getRepository(Diagram::class)->find($diagramId);
            //set the adjacencymatrix for the diagram
            $diagram->setAdjacencyMatrix($adjacencyMatrix);
            $em->persist($diagram);
            $em->flush();


            $taskData[] = [
                'task' => $task,
                'diagram' => $diagram,
                'diagramId' => $task->getPertChart()->getId(),
                'edges' => $edges,
                'predecessors' => $predecessor,
                'adjacencyMatrix' => $adjacencyMatrix[$taskIndices[$task->getId()]],
                'level' => $task->getLevel(),
            ];
        }
        

        return $this->render('edge/show.html.twig', [
            'taskData' => $taskData,
            'pertChartData' => $pertChartData,
            'pertChartDataEdges' => $pertChartDataEdges,
        ]);
    }

    private function calculateAndSaveLevels(array $tasks, array $adjacencyMatrix, EntityManagerInterface $em): void
{
    $levels = [];

    foreach ($tasks as $task) {
        $this->calculateTaskLevel($task, $tasks, $adjacencyMatrix, $levels);
    }

    // Save levels to the database
    foreach ($levels as $taskId => $level) {
        $task = $em->getRepository(Task::class)->find($taskId);
        if ($task) {
            $task->setLevel($level);
            $em->persist($task);
        }
    }

    $em->flush();
}

private function calculateTaskLevel(Task $currentTask, array $tasks, array $adjacencyMatrix, array &$levels): int
{
    $currentTaskId = $currentTask->getId();
    if (isset($levels[$currentTaskId])) {
        return $levels[$currentTaskId];
    }

    $currentTaskIndex = array_search($currentTask, $tasks);
    $maxPredecessorLevel = 0;

    foreach ($tasks as $predecessorTask) {
        $predecessorIndex = array_search($predecessorTask, $tasks);

        if ($adjacencyMatrix[$currentTaskIndex][$predecessorIndex] == 1) {
            $predecessorLevel = $this->calculateTaskLevel($predecessorTask, $tasks, $adjacencyMatrix, $levels);
            $maxPredecessorLevel = max($maxPredecessorLevel, $predecessorLevel);
        }
    }

    $currentTaskLevel = $maxPredecessorLevel + 1;
    $levels[$currentTaskId] = $currentTaskLevel;

    return $currentTaskLevel;
}



    /**
     * Check if a task has predecessors in the adjacency matrix
     */
    private function hasPredecessors(int $taskIndex, array $adjacencyMatrix): bool
    {
        foreach ($adjacencyMatrix[$taskIndex] as $value) {
            if ($value == 1) {
                return true;
            }
        }

        return false;
    }
}




  






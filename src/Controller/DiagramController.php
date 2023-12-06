<?php

namespace App\Controller;

use App\Entity\Edge;
use App\Entity\Task;
use App\Entity\Diagram;
use App\Entity\Project;
use App\Form\ChartType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Process\Process;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DiagramController extends AbstractController
{
    #[Route('/diagram', name: 'app_diagram')]
    public function index(): Response
    {
        return $this->render('diagram/index.html.twig', [
            'controller_name' => 'DiagramController',
        ]);
    }

    /**
     * @Route("/diagram/{projectId}/show", name="show_diagram")
     * @param $projectId
     * @param EntityManagerInterface $em
     * @return Response
     * Create a new diagram
     */ 
    #[Route('/diagram/{projectId}/new', name: 'create_diagram')]
    public function createDiagram($projectId, Request $request, Diagram $diagram = NULL, EntityManagerInterface $em): Response
    {
        if(!$diagram){
            $diagram = new Diagram();
        }
        $project = $em->getRepository(Project::class)->find($projectId);
        $user = $this->getUser();
        $diagram->setCreatedAt(new \DateTime());
        $form = $this->createForm(ChartType::class, $diagram);
        $form->handleRequest($request);
        $showForm = true;
        if($form->isSubmitted() && $form->isValid()){
            $diagram = $form->getData();
            $diagram->setProject($project);
            $diagram->setUser($user);
            $em->persist($diagram);
            $em->flush();
            $showForm = false;
            return $this->redirectToRoute('task/index.html.twig');
        }
        return $this->render('diagram/create.html.twig', [
            'formDiagram' => $form,
            'showForm' => $showForm,
            'projectId' => $projectId,
        ]);
        
    }

    /**
     * Route('/diagram/{diagramId}/dates', name: 'cal_dates')
     * @param EntityManagerInterface $em
     * calculate the dates of the tasks
     * ES,EF,LS,LF,MLand MT
     * @return Response
     */
    #[Route('/diagram/{diagramId}/dates', name: 'cal_dates')]
    public function calculateDates(EntityManagerInterface $em, $diagramId): Response
    {
    // Fetch all tasks of the diagram
    $tasks = $em->getRepository(Task::class)->findBy(['pertChart' => $diagramId]);

    // Initialize arrays to store Early Start (ES) and Early Finish (EF) of each task
    $ES = [];
    $EF = [];

    // Stock the tasks in an array ordered by level
    $tasksByLevel = [];
    foreach ($tasks as $task) {
        $tasksByLevel[$task->getLevel()][] = $task;
    }

    // Sort the tasks by their level
    ksort($tasksByLevel);

    // Calculate the ES and EF of each task
    // Set the ES of the tasks of level 1 to 0
    foreach ($tasksByLevel[1] as $task) {
        $ES[$task->getId()] = 0;
        $EF[$task->getId()] = $task->getDuree();
    }

    // Calculate the ES and EF of the other tasks
    foreach ($tasksByLevel as $level => $tasksAtLevel) {
        if ($level == 1) {
            // Skip tasks of level 1 (already initialized)
            continue;
        }

        foreach ($tasksAtLevel as $task) {
            $taskId = $task->getId();

            // Calculate ES as the maximum EF of all tasks at the previous level
            $maxEF = 0;
            foreach ($tasksByLevel[$level - 1] as $predecessor) {
                $predecessorId = $predecessor->getId();
                $maxEF = max($maxEF, $EF[$predecessorId]);
            }

            $ES[$taskId] = $maxEF;
            $EF[$taskId] = $maxEF + $task->getDuree();
        }
    }

    return $this->render('diagram/index.html.twig', [
        'tasksByLevel' => $tasksByLevel,
        'diagramId' => $diagramId,
        'ES' => $ES,
        'EF' => $EF,
    ]);
}

    #[Route('/diagram/{diagramId}/draw', name: 'draw_graph')]
    // Your Symfony controller method
    public function generatePertChart($diagramId, EntityManagerInterface $em)
    {
    // Fetch tasks and edges based on $diagramId
    $tasks = $em->getRepository(Task::class)->findBy(['pertChart' => $diagramId]);
    $edges = $em->getRepository(Edge::class)->findAll();
    $diagram = $em->getRepository(Diagram::class)->find($diagramId);
    $imgName = str_replace(' ', '_', $diagram->getTitle());

    // Create DOT file content
    $dotContent = "digraph PERT {\n";

    foreach ($tasks as $task) {
        $dotContent .= "  {$task->getId()} [label=\"{$task->getName()} ({$task->getDuree()} days)\"]\n";
    }

    foreach ($edges as $edge) {
        $dotContent .= "  {$edge->getPredecessor()->getId()} -> {$edge->getTask()->getId()}\n";
    }

    $dotContent .= "}\n";

    // Save DOT file
    $dotFilePath = '/Applications/MAMP/htdocs/KOUZEHA_Ammar/PertFinder/public/charts/' . $imgName . '.dot';
    file_put_contents($dotFilePath, $dotContent);

    // Generate image using Graphviz
    $imageFilePath = '/Applications/MAMP/htdocs/KOUZEHA_Ammar/PertFinder/public/charts/' . $imgName . '.png';;
    $process = new Process(['dot', '-Tpng', "-o$imageFilePath", $dotFilePath]);
    $process->run();

    // Check for errors
    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }

    //return new BinaryFileResponse($imageFilePath);
    return $this->render('diagram/chart.html.twig', [
        'imageFilePath' => $imgName . '.png',
        'diagramId' => $diagramId,
    ]);
}




}

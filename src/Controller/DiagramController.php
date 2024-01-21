<?php

namespace App\Controller;

use App\Entity\Edge;
use App\Entity\Task;
use App\Entity\Diagram;
use App\Entity\Project;
use App\Form\ChartType;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Component\Process\Process;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
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
        $diagram->setDuree(0);
        $diagram->setVariance(0);
        $form = $this->createForm(ChartType::class, $diagram);
        $form->handleRequest($request);
        $showForm = true;
        if($form->isSubmitted() && $form->isValid()){
            $diagram = $form->getData();
            $diagram->setProject($project);
            $diagram->setUser($user);
            $imgName = str_replace(' ', '_', $diagram->getTitle());
            $diagram->setImgName($imgName);
            $em->persist($diagram);
            $em->flush();
            $showForm = false;
            $this->addFlash('success', 'Le diagramme a été créé avec succès');
            $diagramId = $diagram->getId();
            return $this->redirectToRoute('new_task', ['diagramId' => $diagramId]);
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
    public function calculateDates(EntityManagerInterface $em,LoggerInterface $logger, $diagramId): Response
    {
    // Fetch all tasks of the diagram
    $tasks = $em->getRepository(Task::class)->findBy(['pertChart' => $diagramId]);
    $edges = $em->getRepository(Edge::class)->findAllEdgesForChart($diagramId);

    // Initialize arrays to store Early Start (ES) and Early Finish (EF) of each task
    // Late Start (LS) and Late Finish (LF) of each task
    // Margin Total (MT) and Margin Libre (ML) of each task
    $ES = [];
    $EF = [];
    $LS = [];
    $LF = [];
    $MT = [];
    $ML = [];

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
    // Calculate LS and LF of each task
    // Set LF of the tasks of the last level to the EF of the last task
    $lastLevel = max(array_keys($tasksByLevel));
    $lastTask = end($tasksByLevel[$lastLevel]);
    $LF[$lastTask->getId()] = $EF[$lastTask->getId()];
    $LS[$lastTask->getId()] = $LF[$lastTask->getId()] - $lastTask->getDuree();

    // Calculate LS and LF of the other tasks
    for ($level = $lastLevel - 1; $level >= 1; $level--) {
        foreach ($tasksByLevel[$level] as $task) {
            $taskId = $task->getId();

            // Calculate LF as the minimum LS of all tasks at the next level
            $minLS = PHP_INT_MAX;
            /* foreach ($tasksByLevel[$level + 1] as $successor) {
                $successorId = $successor->getId();
                $minLS = min($minLS, $LS[$successorId]);
            } */
            $predecessors = $em->getRepository(Edge::class)->findBy(['predecessor' => $task]);
            foreach ($predecessors as $predecessor) {
                $successorId = $predecessor->getTask()->getId();
                $minLS = min($minLS, $LS[$successorId]);
            }

            $LF[$taskId] = $minLS;
            $LS[$taskId] = $LF[$taskId] - $task->getDuree();
        }
    }
       
    //calculate MT and ML of each task
    foreach ($tasks as $task) {
        $taskId = $task->getId();
        $MT[$taskId] = $LF[$taskId] - $EF[$taskId];
        $ML[$taskId] = $LS[$taskId] - $ES[$taskId];
        

    }

    return $this->render('diagram/index.html.twig', [
        'tasksByLevel' => $tasksByLevel,
        'diagramId' => $diagramId,
        'ES' => $ES,
        'EF' => $EF,
        'LS' => $LS,
        'LF' => $LF,
        'MT' => $MT,
        'ML' => $ML,
    ]);
}

    private function calculDatesInternal($diagramId, EntityManagerInterface $em)
    {
        // Fetch all tasks of the diagram
    $tasks = $em->getRepository(Task::class)->findBy(['pertChart' => $diagramId]);
    $edges = $em->getRepository(Edge::class)->findAllEdgesForChart($diagramId);
    // Initialize arrays to store Early Start (ES) and Early Finish (EF) of each task
    // Late Start (LS) and Late Finish (LF) of each task
    // Margin Total (MT) and Margin Libre (ML) of each task
    $ES = [];
    $EF = [];
    $LS = [];
    $LF = [];
    $MT = [];
    $ML = [];

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
    // Calculate LS and LF of each task
    // Set LF of the tasks of the last level to the EF of the last task
    $lastLevel = max(array_keys($tasksByLevel));
    $lastTask = end($tasksByLevel[$lastLevel]);
    $LF[$lastTask->getId()] = $EF[$lastTask->getId()];
    $LS[$lastTask->getId()] = $LF[$lastTask->getId()] - $lastTask->getDuree();

    // Calculate LS and LF of the other tasks
    for ($level = $lastLevel - 1; $level >= 1; $level--) {
        foreach ($tasksByLevel[$level] as $task) {
            $taskId = $task->getId();

            // Calculate LF as the minimum LS of all tasks at the next level
            $minLS = PHP_INT_MAX;
            $predecessors = $em->getRepository(Edge::class)->findBy(['predecessor' => $task]);
            foreach ($predecessors as $predecessor) {
                $successorId = $predecessor->getTask()->getId();
                $minLS = min($minLS, $LS[$successorId]);
            }

            $LF[$taskId] = $minLS;
            $LS[$taskId] = $LF[$taskId] - $task->getDuree();
        }
    }
    //calculate MT and ML of each task
    foreach ($tasks as $task) {
        $taskId = $task->getId();
        $MT[$taskId] = $LF[$taskId] - $EF[$taskId];
        $ML[$taskId] = $LS[$taskId] - $ES[$taskId];
    }
        $results = [
            'ES' => $ES,
            'EF' => $EF,
            'LS' => $LS,
            'LF' => $LF,
            'MT' => $MT,
            'ML' => $ML,
        ];
        return $results;
    }

    #[Route('/diagram/{diagramId}/draw', name: 'draw_graph')]
    public function generatePertChart($diagramId, EntityManagerInterface $em): Response
    {
    // Fetch tasks and edges based on $diagramId
    $tasks = $em->getRepository(Task::class)->findBy(['pertChart' => $diagramId]);
    $edges = $em->getRepository(Edge::class)->findAllEdgesForChart($diagramId);
    $diagram = $em->getRepository(Diagram::class)->find($diagramId);
    //$imgName = str_replace(' ', '_', $diagram->getTitle());
    $imgName = $diagram->getImgName();
    $dates = $this->calculDatesInternal($diagramId, $em);
    $ES = $dates['ES'];
    $EF = $dates['EF'];
    $LS = $dates['LS'];
    $LF = $dates['LF'];
    $MT = $dates['MT'];
    $ML = $dates['ML'];
    
 
    $dotContent = $this->generateDotFileContent($tasks, $edges, $ES, $EF, $LS, $LF, $MT, $ML);

    // Save DOT file
    $dotFilePath = '/Applications/MAMP/htdocs/KOUZEHA_Ammar/PertFinder/public/charts/' . $imgName . '.dot';
    file_put_contents($dotFilePath, $dotContent);

    // Generate image using Graphviz
    $imageFilePath = '/Applications/MAMP/htdocs/KOUZEHA_Ammar/PertFinder/public/charts/img/' . $imgName . '.png';
    $process = new Process(['dot', '-Tpng', "-o$imageFilePath", $dotFilePath]);
    $process->run();

    //Generate pdf file using Graphviz
    $pdfFilePath = '/Applications/MAMP/htdocs/KOUZEHA_Ammar/PertFinder/public/charts/pdf/' . $imgName . '.pdf';
    $process = new Process(['dot', '-Tpdf', "-o$pdfFilePath", $dotFilePath]);
    $process->run();

    // Check for errors
    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }

    //return new BinaryFileResponse($imageFilePath);
    return $this->render('diagram/chart.html.twig', [
        'imageFilePath' => $imgName . '.png',
        'pdfFilePath' => $imgName . '.pdf',
        'diagramId' => $diagramId,
    ]);
}

public function generateDotFileContent($tasks, $edges, $ES, $EF, $LS, $LF, $MT, $ML)
{
    
    // Create DOT file content
    $dotContent = "digraph PERT {\n";
    // Set left-to-right orientation
    $dotContent .= "  rankdir=LR;\n";
    $dotContent .= "  ranksep=1;\n";
    
    // Set node shape to record
    $dotContent .= "  node [shape=record];\n";
    // Set edge style to vee
    $dotContent .= "  edge [arrowhead=vee];\n";
    //background color
    $dotContent .= "  bgcolor=\"#ECE9E9\";\n";

    foreach ($tasks as $task) {
        //the shape of the node
        $dotContent .= "  {$task->getId()} [label=<<TABLE BORDER='0' CELLBORDER='1' CELLSPACING='0' CELLPADDING='4'>
        <TR><TD BGCOLOR= 'aquamarine3'>ES: {$ES[$task->getId()]}</TD><TD BGCOLOR= 'antiquewhite3'>Duree: {$task->getDuree()}</TD><TD BGCOLOR= 'aquamarine3'>EF: {$EF[$task->getId()]}</TD></TR>
        <TR><TD BGCOLOR= 'white' COLSPAN='3'>{$task->getName()}</TD></TR>
        <TR><TD BGCOLOR= 'cornflowerblue'>LS: {$LS[$task->getId()]}</TD><TD BGCOLOR= 'chocolate2'>MT: {$MT[$task->getId()]}</TD><TD BGCOLOR= 'cornflowerblue'>LF: {$LF[$task->getId()]}</TD></TR>
        
        </TABLE>>]\n";
        /* $dotContent .= "  {$task->getId()} [label=\"{MT: {$MT[$task->getId()]} |LS: {$LS[$task->getId()]}| ML: {$ML[$task->getId()]}} | {ES: {$ES[$task->getId()]} | ED: {$task->getDuree()} | EF: {$EF[$task->getId()]} }|{{$task->getName()} | LF: {$LF[$task->getId()]} | }\"]\n"; */

    }

    //set the critical path in red if MT = 0

    foreach ($edges as $edge) {
        if($MT[$edge->getTask()->getId()] == 0 and $MT[$edge->getPredecessor()->getId()] == 0){
            $dotContent .= "  {$edge->getPredecessor()->getId()} -> {$edge->getTask()->getId()} [color=red]\n";
        }
        else
        $dotContent .= "  {$edge->getPredecessor()->getId()} -> {$edge->getTask()->getId()}\n";
    }

    $dotContent .= "}\n";

    return $dotContent;
}

    #[Route('/diagram/{diagramId}/download/{fileName}', name: 'download_diagram')]
    public function downloadImageFile($fileName)
    {
        $fPath = $this->getParameter('kernel.project_dir') . '/public/charts/img/' . $fileName;
        
        // Check if the file exists
        if (!file_exists($fPath)) {
            throw $this->createNotFoundException('The file does not exist');
        }
        $response = new BinaryFileResponse($fPath);

        // Set the response headers
        $response->headers->set('Content-Type', 'image/png');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $fileName . '"');

        return $response;
    }

    #[Route('/diagram/{diagramId}/download/pdf/{fileName}', name: 'download_pdf')]
    public function downloadPdfFile($fileName)
    {
        $fPath = $this->getParameter('kernel.project_dir') . '/public/charts/pdf/' . $fileName;
        
        // Check if the file exists
        if (!file_exists($fPath)) {
            throw $this->createNotFoundException('The file does not exist');
        }
        $response = new BinaryFileResponse($fPath);

        // Set the response headers
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $fileName . '"');

        return $response;
    }

    #[Route('/diagram/{diagramId}/delete', name: 'delete_diagram')]
    public function deleteDiagramme($diagramId, EntityManagerInterface $em): Response
    {
        
        $diagram = $em->getRepository(Diagram::class)->find($diagramId);
        $user = $diagram->getUser();
        $project = $diagram->getProject();
        if($this->isGranted('ROLE_PROJECT_MANAGER') or $user == $this->getUser()){
            $project->removeDiagram($diagram);
            $user->removeDiagram($diagram);
            $em->remove($diagram);
            $em->flush();
            $this->addFlash('success', 'Le diagramme a été supprimé avec succès');
            return $this->redirectToRoute('app_project');
        }
        else{
            $this->addFlash('warning', 'Vous n\'avez pas le droit de supprimer ce diagramme');
            return $this->redirectToRoute('app_project');
        }
    }

}

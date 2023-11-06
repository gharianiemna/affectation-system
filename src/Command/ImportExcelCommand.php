<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Task;
use App\Service\TaskService;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use DateTime;

class ImportExcelCommand extends Command
{
    protected static $defaultName = 'app:import-excel';

    private $entityManager;
    private $taskService;
    public function __construct(EntityManagerInterface $entityManager, TaskService $taskService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->taskService = $taskService;
    }

    protected function configure()
    {
        $this->setDescription('Import data from an Excel file')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the Excel file to import.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument('file');
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("The file '$filePath' does not exist.");
        }
        $spreadsheet = IOFactory::load($filePath);
        $row = $spreadsheet->getActiveSheet()->removeRow(1); 
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        foreach ($sheetData as $rowData) {
            $task = new Task();
            $format = "m/d/Y H:i:s";
            $task->setType($rowData['A']);
            $task->setName($rowData['B']);
            $task->setCode($rowData['C']);
            $dateString = $rowData['D'] . ' ' . $rowData['E'];
            $task->setStartDate(DateTime::createFromFormat($format, $dateString));
            $task->setDifficulty($this->taskService->taskReview($task));
            $this->entityManager->persist($task);
        }
        $this->entityManager->flush();
        $output->writeln('Data imported successfully.');
        return Command::SUCCESS;
    }
}

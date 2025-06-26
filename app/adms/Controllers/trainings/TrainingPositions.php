<?php

namespace App\adms\Controllers\trainings;

use App\adms\Models\Repository\TrainingsRepository;
use App\adms\Models\Repository\PositionsRepository;
use App\adms\Models\Repository\TrainingPositionsRepository;
use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Views\Services\LoadViewService;

class TrainingPositions
{
    private array|string|null $data = null;

    public function index(int $trainingId): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $obrigatorio = $_POST['obrigatorio'] ?? [];
            $trainingPositionsRepo = new TrainingPositionsRepository();
            $trainingPositionsRepo->saveTrainingPositions($trainingId, $obrigatorio);
            $_SESSION['msg'] = '<div class="alert alert-success">Vínculos atualizados com sucesso!</div>';
            header('Location: ' . $_ENV['URL_ADM'] . 'training-positions/' . $trainingId);
            exit;
        }

        $trainingsRepo = new TrainingsRepository();
        $positionsRepo = new PositionsRepository();
        $trainingPositionsRepo = new TrainingPositionsRepository();

        $training = $trainingsRepo->getTraining($trainingId);
        $positions = $positionsRepo->getAllPositions(1, 1000);
        $linkedPositions = $trainingPositionsRepo->getPositionsByTraining($trainingId);

        $this->data['training'] = $training;
        $this->data['positions'] = $positions;
        $this->data['linkedPositions'] = $linkedPositions;

        $pageElements = [
            'title_head' => 'Vincular Cargos ao Treinamento',
            'menu' => 'list-trainings',
            'buttonPermission' => ['TrainingPositions'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        $loadView = new LoadViewService('adms/Views/trainings/trainingPositions', $this->data);
        $loadView->loadView();
    }
} 
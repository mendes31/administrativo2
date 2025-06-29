<?php

namespace App\adms\Controllers\trainings;

use App\adms\Models\Repository\TrainingsRepository;
use App\adms\Models\Repository\TrainingUsersRepository;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Views\Services\LoadViewService;
use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Models\Repository\TrainingApplicationsRepository;
use App\adms\Helpers\LogHelper;

class ApplyTraining
{
    private array $data = [];

    public function index($param = null): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->apply();
            return;
        }

        // Padrão: /controller/1/1 => $param = '1/1'
        $user_id = null;
        $training_id = null;
        if ($param && preg_match('/^([0-9]+)\/([0-9]+)$/', $param, $matches)) {
            $user_id = $matches[1];
            $training_id = $matches[2];
        } else {
            $user_id = $param ?? ($_GET['user_id'] ?? null);
            $training_id = $_GET['training_id'] ?? null;
        }
        $this->data['user_id'] = $user_id;
        $this->data['training_id'] = $training_id;
        $this->data['edit_id'] = $_GET['edit_id'] ?? null;

        if (!$this->data['training_id'] || !$this->data['user_id']) {
            header("Location: " . $_ENV['URL_ADM'] . "list-training-status");
            exit;
        }

        $trainingsRepo = new TrainingsRepository();
        $usersRepo = new UsersRepository();
        $applicationsRepo = new TrainingApplicationsRepository();

        // Buscar dados básicos
        $this->data['training'] = $trainingsRepo->getTraining($this->data['training_id']);
        $this->data['user'] = $usersRepo->getUser($this->data['user_id']);

        if (!$this->data['training'] || !$this->data['user']) {
            header("Location: " . $_ENV['URL_ADM'] . "list-training-status");
            exit;
        }

        // Se for edição, buscar dados da aplicação
        if ($this->data['edit_id']) {
            $this->data['application'] = $applicationsRepo->getById($this->data['edit_id']);
            if (!$this->data['application']) {
                header("Location: " . $_ENV['URL_ADM'] . "list-training-status");
                exit;
            }
        }

        // Preencher formulário
        $this->data['form_data_realizacao'] = $this->data['application']['data_realizacao'] ?? date('Y-m-d');
        $this->data['form_data_agendada'] = $this->data['application']['data_agendada'] ?? '';
        $this->data['form_nota'] = $this->data['application']['nota'] ?? '';
        $this->data['form_observacoes'] = $this->data['application']['observacoes'] ?? '';
        $this->data['form_instrutor_nome'] = $this->data['application']['instrutor_nome'] ?? '';
        $this->data['form_instrutor_email'] = $this->data['application']['instrutor_email'] ?? '';

        // Elementos de página
        $pageElements = [
            'title_head' => 'Registrar Aplicação de Treinamento',
            'menu' => 'apply-training',
            'buttonPermission' => ['ApplyTraining'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        $loadView = new LoadViewService('adms/Views/trainings/applyTraining', $this->data);
        $loadView->loadView();
    }

    public function apply(): void
    {
        $training_id = (int) ($_POST['training_id'] ?? 0);
        $user_id = (int) ($_POST['user_id'] ?? 0);
        $edit_id = (int) ($_POST['edit_id'] ?? 0);
        
        $data_realizacao = $_POST['data_realizacao'] ?? null;
        $data_agendada = $_POST['data_agendada'] ?? null;
        $nota = $_POST['nota'] ?? null;
        $observacoes = $_POST['observacoes'] ?? null;
        $instrutor_nome = $_POST['instrutor_nome'] ?? null;
        $instrutor_email = $_POST['instrutor_email'] ?? null;
        $aplicado_por = $_SESSION['user_id'] ?? null;

        // Validações básicas
        if (!$training_id || !$user_id) {
            $_SESSION['msg'] = "Dados obrigatórios não informados.";
            $_SESSION['msg_type'] = "danger";
            header("Location: " . $_ENV['URL_ADM'] . "apply-training?training_id={$training_id}&user_id={$user_id}");
            exit;
        }

        // Deve ter pelo menos uma data
        if (!$data_realizacao && !$data_agendada) {
            $_SESSION['msg'] = "Informe a data de realização ou agendamento.";
            $_SESSION['msg_type'] = "danger";
            header("Location: " . $_ENV['URL_ADM'] . "apply-training?training_id={$training_id}&user_id={$user_id}");
            exit;
        }

        // Validações de data
        if ($data_realizacao && $data_realizacao > date('Y-m-d')) {
            $_SESSION['msg'] = "Data de realização não pode ser futura.";
            $_SESSION['msg_type'] = "danger";
            header("Location: " . $_ENV['URL_ADM'] . "apply-training?training_id={$training_id}&user_id={$user_id}");
            exit;
        }

        if ($data_agendada && $data_agendada < date('Y-m-d')) {
            $_SESSION['msg'] = "Data de agendamento não pode ser retroativa.";
            $_SESSION['msg_type'] = "danger";
            header("Location: " . $_ENV['URL_ADM'] . "apply-training?training_id={$training_id}&user_id={$user_id}");
            exit;
        }

        $applicationsRepo = new TrainingApplicationsRepository();
        $trainingUsersRepo = new TrainingUsersRepository();
        $trainingsRepo = new TrainingsRepository();
        
        try {
            $dados = [
                'adms_user_id' => $user_id,
                'adms_training_id' => $training_id,
                'data_realizacao' => $data_realizacao,
                'data_agendada' => $data_agendada,
                'nota' => $nota,
                'observacoes' => $observacoes,
                'instrutor_nome' => $instrutor_nome,
                'instrutor_email' => $instrutor_email,
                'aplicado_por' => $aplicado_por,
                'status' => $data_realizacao ? 'concluido' : 'agendado'
            ];

            if ($edit_id) {
                // Atualização
                $oldData = $applicationsRepo->getById($edit_id);
                $applicationsRepo->update($edit_id, $dados);
                LogHelper::logUpdate('adms_training_applications', $edit_id, $oldData, $dados, $aplicado_por);
                $msg = "Aplicação atualizada com sucesso!";
            } else {
                // Inserção
                $newId = $applicationsRepo->insert($dados);
                LogHelper::log('adms_training_applications', 'inserção', $newId, 'Nova aplicação de treinamento', $aplicado_por);
                $msg = $data_realizacao ? "Treinamento registrado como realizado!" : "Treinamento agendado com sucesso!";
            }

            // Se foi realizado e tem reciclagem, marcar como concluído e criar novo ciclo
            if ($data_realizacao) {
                $training = $trainingsRepo->getTraining($training_id);
                if ($training['reciclagem'] && $training['reciclagem_periodo']) {
                    $trainingUsersRepo->markAsCompleted($user_id, $training_id, true);
                }
            }

            $_SESSION['msg'] = $msg;
            $_SESSION['msg_type'] = "success";
            header("Location: " . $_ENV['URL_ADM'] . "list-training-status");
            
        } catch (\Exception $e) {
            $_SESSION['msg'] = "Erro: " . $e->getMessage();
            $_SESSION['msg_type'] = "danger";
            header("Location: " . $_ENV['URL_ADM'] . "apply-training?training_id={$training_id}&user_id={$user_id}");
        }
        exit;
    }
} 
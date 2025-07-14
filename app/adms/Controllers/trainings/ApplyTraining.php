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
        // Se houver dados do formulário em sessão, repopular
        if (!empty($_SESSION['form_apply_training'])) {
            foreach ($_SESSION['form_apply_training'] as $key => $value) {
                $this->data[$key] = $value;
            }
            unset($_SESSION['form_apply_training']);
        }

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

        // Buscar lista de usuários para select de instrutor interno
        $this->data['listUsers'] = $usersRepo->getAllUsersSelect();

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
        
        // Determinar tipo de instrutor para preencher o select corretamente
        $this->data['form_instructor_type'] = '';
        $this->data['form_instructor_user_id'] = '';
        if (!empty($this->data['application']['instrutor_nome']) && !empty($this->data['application']['instrutor_email'])) {
            // Verificar se é um usuário interno
            foreach ($this->data['listUsers'] as $user) {
                if ($user['name'] === $this->data['application']['instrutor_nome'] && 
                    $user['email'] === $this->data['application']['instrutor_email']) {
                    $this->data['form_instructor_type'] = 'internal';
                    $this->data['form_instructor_user_id'] = $user['id'];
                    break;
                }
            }
            // Se não encontrou como interno, é externo
            if (empty($this->data['form_instructor_type'])) {
                $this->data['form_instructor_type'] = 'external';
            }
        }

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
        // Antes de cada redirecionamento de erro, salvar os dados do formulário em sessão
        function saveFormSession() {
            $_SESSION['form_apply_training'] = [
                'form_data_realizacao' => $_POST['data_realizacao'] ?? '',
                'form_data_agendada' => $_POST['data_agendada'] ?? '',
                'form_nota' => $_POST['nota'] ?? '',
                'form_observacoes' => $_POST['observacoes'] ?? '',
                'form_instrutor_nome' => $_POST['instrutor_nome'] ?? '',
                'form_instrutor_email' => $_POST['instrutor_email'] ?? '',
                'form_instructor_type' => $_POST['instructor_type'] ?? '',
                'form_instructor_user_id' => $_POST['instructor_user_id'] ?? '',
            ];
        }

        $training_id = (int) ($_POST['training_id'] ?? 0);
        $user_id = (int) ($_POST['user_id'] ?? 0);
        $edit_id = (int) ($_POST['edit_id'] ?? 0);
        
        $data_realizacao = $_POST['data_realizacao'] ?? null;
        $data_avaliacao = $_POST['data_avaliacao'] ?? null;
        $data_agendada = $_POST['data_agendada'] ?? null;
        $nota = $_POST['nota'] ?? null;
        $observacoes = $_POST['observacoes'] ?? null;
        $instructor_type = $_POST['instructor_type'] ?? null;
        $instructor_user_id = (int) ($_POST['instructor_user_id'] ?? 0);
        $instrutor_nome = $_POST['instrutor_nome'] ?? null;
        $instrutor_email = $_POST['instrutor_email'] ?? null;
        $aplicado_por = $_SESSION['user_id'] ?? null;

        $redirectUrl = $_ENV['URL_ADM'] . "apply-training?training_id={$training_id}&user_id={$user_id}";
        if (!empty($edit_id)) {
            $redirectUrl .= "&edit_id={$edit_id}";
        }

        // Validações básicas
        if (!$training_id || !$user_id) {
            $_SESSION['msg'] = "Dados obrigatórios não informados.";
            $_SESSION['msg_type'] = "danger";
            saveFormSession();
            header("Location: " . $redirectUrl);
            exit;
        }
        // Validação obrigatória da nota
        if ($nota === null || $nota === '' || !is_numeric($nota) || $nota < 0 || $nota > 10) {
            $_SESSION['msg'] = "O campo Nota é obrigatório e deve estar entre 0 e 10.";
            $_SESSION['msg_type'] = "danger";
            saveFormSession();
            header("Location: " . $redirectUrl);
            exit;
        }
        // Validação de data de avaliação
        if ($data_avaliacao) {
            if ($data_realizacao && $data_avaliacao < $data_realizacao) {
                $_SESSION['msg'] = "A Data de Avaliação não pode ser anterior à Data de Realização.";
                $_SESSION['msg_type'] = "danger";
                saveFormSession();
                header("Location: " . $redirectUrl);
                exit;
            }
            if ($data_avaliacao > date('Y-m-d')) {
                $_SESSION['msg'] = "A Data de Avaliação não pode ser futura.";
                $_SESSION['msg_type'] = "danger";
                saveFormSession();
                header("Location: " . $redirectUrl);
                exit;
            }
        }

        // Deve ter pelo menos uma data
        if (!$data_realizacao && !$data_agendada) {
            $_SESSION['msg'] = "Informe a data de realização ou agendamento.";
            $_SESSION['msg_type'] = "danger";
            saveFormSession();
            header("Location: " . $redirectUrl);
            exit;
        }

        // Validações de data
        if ($data_realizacao && $data_realizacao > date('Y-m-d')) {
            $_SESSION['msg'] = "Data de realização não pode ser futura.";
            $_SESSION['msg_type'] = "danger";
            saveFormSession();
            header("Location: " . $redirectUrl);
            exit;
        }

        if ($data_agendada && $data_agendada < date('Y-m-d')) {
            $_SESSION['msg'] = "Data de agendamento não pode ser retroativa.";
            $_SESSION['msg_type'] = "danger";
            saveFormSession();
            header("Location: " . $redirectUrl);
            exit;
        }

        // Validação obrigatória do tipo de instrutor
        if (empty($instructor_type)) {
            $_SESSION['msg'] = "Selecione o tipo de instrutor.";
            $_SESSION['msg_type'] = "danger";
            saveFormSession();
            header("Location: " . $redirectUrl);
            exit;
        }
        // Validação obrigatória do instrutor conforme o tipo
        if ($instructor_type === 'internal') {
            if (empty($instructor_user_id)) {
                $_SESSION['msg'] = "Selecione o instrutor interno.";
                $_SESSION['msg_type'] = "danger";
                saveFormSession();
                header("Location: " . $redirectUrl);
                exit;
            }
        } elseif ($instructor_type === 'external') {
            if (empty($instrutor_nome) || empty($instrutor_email)) {
                $_SESSION['msg'] = "Informe o nome e e-mail do instrutor externo.";
                $_SESSION['msg_type'] = "danger";
                saveFormSession();
                header("Location: " . $redirectUrl);
                exit;
            }
        }

        // Processar dados do instrutor
        $real_instructor_nome = null;
        $real_instructor_email = null;
        $instructor_user_id_to_save = null;
        if (!empty($instructor_user_id)) {
            // Instrutor interno - buscar nome e e-mail do usuário
            $usersRepo = new UsersRepository();
            $user = $usersRepo->getUser((int)$instructor_user_id);
            if ($user) {
                $real_instructor_nome = $user['name'];
                $real_instructor_email = $user['email'];
                $instructor_user_id_to_save = $user['id'];
            }
        } elseif (!empty($instrutor_nome)) {
            // Instrutor externo
            $real_instructor_nome = $instrutor_nome;
            $real_instructor_email = $instrutor_email;
            $instructor_user_id_to_save = null;
        }

        $applicationsRepo = new TrainingApplicationsRepository();
        $trainingUsersRepo = new TrainingUsersRepository();
        $trainingsRepo = new TrainingsRepository();
        
        try {
            $dados = [
                'adms_user_id' => $user_id,
                'adms_training_id' => $training_id,
                'data_realizacao' => $data_realizacao,
                'data_avaliacao' => $data_avaliacao,
                'data_agendada' => $data_agendada,
                'nota' => $nota,
                'observacoes' => $observacoes,
                'instrutor_nome' => $instrutor_nome,
                'instrutor_email' => $instrutor_email,
                'instructor_user_id' => $instructor_user_id_to_save,
                'real_instructor_nome' => $real_instructor_nome,
                'real_instructor_email' => $real_instructor_email,
                'aplicado_por' => $aplicado_por,
                'status' => $data_realizacao ? 'concluido' : 'agendado'
            ];
           // var_dump($dados); exit;

            // Atualizar vínculo principal
            $trainingUsersRepo->applyTraining($user_id, $training_id, [
                'data_realizacao' => $data_realizacao,
                'data_agendada' => $data_agendada,
                'nota' => $nota,
                'observacoes' => $observacoes,
                'status' => $data_realizacao ? 'concluido' : 'agendado'
            ]);

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
            header("Location: " . $redirectUrl);
        }
        exit;
    }
} 
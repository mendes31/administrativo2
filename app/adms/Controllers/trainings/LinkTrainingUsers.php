<?php
namespace App\adms\Controllers\trainings;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Models\Repository\TrainingUsersRepository;
use App\adms\Views\Services\LoadViewService;

class LinkTrainingUsers
{
    private array|string|null $data = null;

    public function index(int|string $id)
    {
        if (!is_numeric($id)) {
            // Se não for numérico, redireciona para a listagem
            header('Location: ' . $_ENV['URL_ADM'] . 'list-trainings');
            exit;
        }
        $id = (int)$id;
        $usersRepo = new UsersRepository();
        $this->data['training_id'] = $id;

        // Buscar informações do treinamento
        $trainingsRepo = new \App\adms\Models\Repository\TrainingsRepository();
        $training = $trainingsRepo->getTraining($id);
        $this->data['training_info'] = [
            'nome' => $training['nome'] ?? '',
            'codigo' => $training['codigo'] ?? '',
            'versao' => $training['versao'] ?? '',
            'tipo' => $training['tipo'] ?? '',
        ];

        // Buscar cargos obrigatórios para o treinamento
        $positionsRepo = new \App\adms\Models\Repository\TrainingPositionsRepository();
        $cargosObrigatorios = $positionsRepo->getPositionIdsByTraining($id);

        // Sincronizar vínculos automáticos para todos os usuários em cargos obrigatórios
        if (!empty($cargosObrigatorios)) {
            $usersRepoAll = new \App\adms\Models\Repository\UsersRepository();
            $todosUsuarios = $usersRepoAll->getAllUsers(1, 10000); // Busca todos os usuários
            $trainingUsersRepoSync = new TrainingUsersRepository();
            foreach ($todosUsuarios as $usuario) {
                if (in_array($usuario['user_position_id'], $cargosObrigatorios)) {
                    $trainingUsersRepoSync->insertOrUpdate($usuario['id'], $id, 'dentro_do_prazo', 'cargo');
                }
            }
            // Atualizar matriz de treinamentos para todos os usuários
            $matrixService = new \App\adms\Controllers\trainings\TrainingMatrixService();
            $matrixService->updateMatrixForAllUsers();
        }

        // Buscar usuários já vinculados (direto ou por cargo obrigatório)
        $trainingUsersRepo = new TrainingUsersRepository();
        $this->data['vinculados'] = $trainingUsersRepo->getAllVinculadosPorTreinamento($id);
        $vinculadosIds = array_column($this->data['vinculados'], 'id');

        // Buscar todos os usuários
        $todos = $usersRepo->getAllUsersSelect();
        // Filtrar: só mostrar no select quem NÃO está em cargos obrigatórios E NÃO está vinculado direto
        $this->data['users'] = array_filter($todos, function($u) use ($vinculadosIds, $cargosObrigatorios) {
            return !in_array($u['id'], $vinculadosIds) && !in_array($u['user_position_id'] ?? null, $cargosObrigatorios);
        });

        $pageElements = [
            'title_head' => 'Vincular Colaboradores ao Treinamento',
            'menu' => 'list-trainings',
            'buttonPermission' => ['LinkTrainingUsers'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        $loadView = new LoadViewService("adms/Views/trainings/linkTrainingUsers", $this->data);
        $loadView->loadView();
    }

    // Nova página para exibir confirmação de vínculo (GET)
    public function storePage()
    {
        // Apenas exibe uma página de confirmação ou redireciona
        header('Location: ' . $_ENV['URL_ADM'] . 'list-trainings');
        exit;
    }

    // Processa o POST de vínculo
    public function store()
    {
        die('CHEGOU NO STORE');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $_ENV['URL_ADM'] . 'list-trainings');
            exit;
        }
        $trainingId = isset($_POST['training_id']) ? (int)$_POST['training_id'] : null;
        $userIds = $_POST['user_ids'] ?? [];
        // LOG DE DEPURAÇÃO
        echo '<pre style="color:red;">DEBUG POST:\n';
        echo 'trainingId: ' . var_export($trainingId, true) . "\n";
        echo 'userIds: ' . var_export($userIds, true) . "\n";
        echo '</pre>';
        // FIM LOG
        if ($trainingId && !empty($userIds)) {
            $repo = new TrainingUsersRepository();
            $repo->vincularUsuariosTreinamento($trainingId, $userIds);
            $_SESSION['msg'] = '<div class="alert alert-success">Colaboradores vinculados com sucesso!</div>';
            header('Location: ' . $_ENV['URL_ADM'] . 'list-trainings');
            exit;
        } else {
            $_SESSION['msg'] = '<div class="alert alert-danger">Selecione pelo menos um colaborador.</div>';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
} 
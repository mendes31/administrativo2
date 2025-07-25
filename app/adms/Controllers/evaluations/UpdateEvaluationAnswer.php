<?php

namespace App\adms\Controllers\evaluations;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Models\Repository\EvaluationAnswersRepository;
use App\adms\Models\Repository\EvaluationModelsRepository;
use App\adms\Models\Repository\EvaluationQuestionsRepository;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Views\Services\LoadViewService;
use App\adms\Helpers\CSRFHelper;

/**
 * Controller para editar respostas de avaliação.
 *
 * @package App\adms\Controllers\evaluations
 * @author Rafael Mendes
 */
class UpdateEvaluationAnswer
{
    /** @var array|string|null $data Dados enviados para a VIEW */
    private array|string|null $data = null;
    /** @var array|null $dataForm Dados do formulário */
    private array|null $dataForm = null;

    public function index(int $id): void
    {
        $this->dataForm = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (!empty($this->dataForm['SendUpdateEvaluationAnswer'])) {
            unset($this->dataForm['SendUpdateEvaluationAnswer']);

            // Validar CSRF token
            if (!CSRFHelper::validateCSRFToken('form_update_evaluation_answer', $this->dataForm['csrf_token'])) {
                $this->data['error'] = "Token de segurança inválido. Tente novamente.";
            } else {
                // Validar dados
                $this->validateData();

                // Se não houver erro, salvar no banco
                if (empty($this->data['error'])) {
                    $this->updateAnswer($id);
                }
            }
        } else {
            $viewEvaluationAnswer = new EvaluationAnswersRepository();
            $this->data['form'] = $viewEvaluationAnswer->getAnswerById($id);
        }

        if (!$this->data['form']) {
            header('Location: ' . getenv('URL_ADM') . 'list-evaluation-answers/index');
            exit;
        }

        $this->loadFormData();
        $this->viewUpdateEvaluationAnswer();
    }

    /**
     * Validar os dados do formulário.
     *
     * @return void
     */
    private function validateData(): void
    {
        // Validar usuário
        if (empty($this->dataForm['usuario_id'])) {
            $this->data['error'] = "Erro: Selecione um usuário!";
            return;
        }

        // Validar modelo
        if (empty($this->dataForm['evaluation_model_id'])) {
            $this->data['error'] = "Erro: Selecione um modelo de avaliação!";
            return;
        }

        // Validar pergunta
        if (empty($this->dataForm['evaluation_question_id'])) {
            $this->data['error'] = "Erro: Selecione uma pergunta!";
            return;
        }

        // Validar resposta
        if (empty($this->dataForm['resposta'])) {
            $this->data['error'] = "Erro: Digite a resposta!";
            return;
        }

        if (strlen($this->dataForm['resposta']) < 3) {
            $this->data['error'] = "Erro: A resposta deve ter pelo menos 3 caracteres!";
            return;
        }

        // Validar pontuação
        if (!empty($this->dataForm['pontuacao'])) {
            if (!is_numeric($this->dataForm['pontuacao']) || $this->dataForm['pontuacao'] < 0 || $this->dataForm['pontuacao'] > 10) {
                $this->data['error'] = "Erro: A pontuação deve ser um número entre 0 e 10!";
                return;
            }
        }

        // Validar status
        $statusValidos = ['ativo', 'inativo'];
        if (!empty($this->dataForm['status']) && !in_array($this->dataForm['status'], $statusValidos)) {
            $this->data['error'] = "Erro: Status inválido!";
            return;
        }

        // Se chegou até aqui, não há erros
        $this->data['error'] = null;
    }

    /**
     * Atualizar a resposta no banco de dados.
     *
     * @param int $id ID da resposta
     * @return void
     */
    private function updateAnswer(int $id): void
    {
        // Preparar dados para salvar
        $data = [
            'usuario_id' => (int) $this->dataForm['usuario_id'],
            'evaluation_model_id' => (int) $this->dataForm['evaluation_model_id'],
            'evaluation_question_id' => (int) $this->dataForm['evaluation_question_id'],
            'resposta' => trim($this->dataForm['resposta']),
            'pontuacao' => !empty($this->dataForm['pontuacao']) ? (float) $this->dataForm['pontuacao'] : null,
            'comentario' => !empty($this->dataForm['comentario']) ? trim($this->dataForm['comentario']) : null,
            'status' => $this->dataForm['status'] ?? 'ativo'
        ];

        // Instanciar o repository
        $answersRepository = new EvaluationAnswersRepository();

        // Tentar atualizar a resposta
        if ($answersRepository->update($data, $id)) {
            $urlRedirect = getenv('URL_ADM') . 'list-evaluation-answers/index';
            header("Location: $urlRedirect");
            exit;
        } else {
            $this->data['error'] = "Erro: Não foi possível atualizar a resposta. Tente novamente!";
        }
    }

    /**
     * Carregar dados para o formulário
     */
    private function loadFormData(): void
    {
        $usersRepository = new UsersRepository();
        $modelsRepository = new EvaluationModelsRepository();
        $questionsRepository = new EvaluationQuestionsRepository();

        $this->data['users'] = $usersRepository->getAllUsers();
        $this->data['models'] = $modelsRepository->getAllModels();
        $this->data['questions'] = $questionsRepository->getAllQuestions();
    }

    /**
     * Carregar a VIEW
     */
    private function viewUpdateEvaluationAnswer(): void
    {
        $pageElements = [
            'title_head' => 'Editar Resposta de Avaliação',
            'menu' => 'update-evaluation-answer',
            'buttonPermission' => ['ListEvaluationAnswers', 'ViewEvaluationAnswer'],
        ];

        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        // Gerar novo token CSRF
        $this->data['csrf_token'] = CSRFHelper::generateCSRFToken('form_update_evaluation_answer');

        $loadView = new LoadViewService('adms/Views/evaluations/answers/update', $this->data);
        $loadView->loadView();
    }
} 
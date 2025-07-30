<?php

namespace App\adms\Controllers\lgpd;

use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Helpers\CSRFHelper;
use App\adms\Models\Repository\LgpdInventoryRepository;
use App\adms\Models\Repository\LgpdRopaRepository;
use App\adms\Models\Repository\LgpdDataMappingRepository;
use App\adms\Models\Repository\DepartmentsRepository;
use App\adms\Views\Services\LoadViewService;

/**
 * Controller para criar ROPA automaticamente a partir do Inventário LGPD
 */
class LgpdRopaCreateFromInventory
{
    private array|string|null $data = null;

    /**
     * Criar ROPA a partir do inventário
     */
    public function index(): void
    {
        // Obter ID do inventário da URL
        $urlParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $inventoryId = end($urlParts);
        
        if (empty($inventoryId) || !is_numeric($inventoryId)) {
            $_SESSION['msg'] = "<div class='alert alert-danger' role='alert'>ID do inventário não fornecido!</div>";
            $urlRedirect = $_ENV['URL_ADM'] . "lgpd-inventory";
            header("Location: $urlRedirect");
            exit();
        }
        
        $inventoryId = (int) $inventoryId;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createRopaFromInventory($inventoryId);
        } else {
            $this->viewCreateFromInventory($inventoryId);
        }
    }
    
    /**
     * Exibir formulário para criar ROPA a partir do inventário
     */
    private function viewCreateFromInventory(int $inventoryId): void
    {
        $inventoryRepo = new LgpdInventoryRepository();
        $inventory = $inventoryRepo->getById($inventoryId);
        
        if (!$inventory) {
            $_SESSION['msg'] = "<div class='alert alert-danger' role='alert'>Inventário não encontrado!</div>";
            $urlRedirect = $_ENV['URL_ADM'] . "lgpd-inventory";
            header("Location: $urlRedirect");
            exit();
        }
        
        // Buscar grupos de dados associados
        $dataGroups = $inventoryRepo->getDataGroupsByInventoryId($inventoryId);
        
        // Preparar dados pré-preenchidos
        $personalData = [];
        $hasSensitiveData = false;
        
        foreach ($dataGroups as $group) {
            $personalData[] = $group['name'];
            if ($group['data_category'] === 'Sensível') {
                $hasSensitiveData = true;
            }
        }
        
        // Carregar dados para o formulário
        $departmentsRepo = new DepartmentsRepository();
        $this->data['departamentos'] = $departmentsRepo->getAllDepartmentsSelect();
        
        // Dados específicos do inventário
        $this->data['inventory'] = $inventory;
        $this->data['data_groups'] = $dataGroups;
        $this->data['prefilled_data'] = [
            'departamento_id' => $inventory['department_id'],
            'data_subject' => $inventory['data_subject'],
            'personal_data' => implode(', ', $personalData),
            'processing_purpose' => 'Processamento baseado no inventário',
            'base_legal' => 'Execução de contrato',
            'retencao' => '5 anos',
            'medidas_seguranca' => 'Acesso restrito, criptografia',
            'sharing' => 'Não há',
            'riscos' => $hasSensitiveData ? 'Alto - Dados sensíveis envolvidos' : 'Médio',
            'observacoes' => $hasSensitiveData ? '⚠️ ATENÇÃO: Dados sensíveis envolvidos. Recomenda-se DPIA.' : null
        ];

        $pageElements = [
            'title_head' => 'Criar ROPA a partir do Inventário',
            'menu' => 'ListLgpdRopa',
            'buttonPermission' => ['ListLgpdRopa'],
        ];
        $pageLayoutService = new PageLayoutService();
        $this->data = array_merge($this->data, $pageLayoutService->configurePageElements($pageElements));

        $loadView = new LoadViewService("adms/Views/lgpd/ropa/create-from-inventory", $this->data);
        $loadView->loadView();
    }
    
    /**
     * Criar ROPA a partir do inventário
     */
    private function createRopaFromInventory(int $inventoryId): void
    {
        $formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        
        if (isset($formData['SendAddRopa'])) {
            unset($formData['SendAddRopa']);
            
            // Adicionar ID do inventário
            $formData['inventory_id'] = $inventoryId;
            
            $ropaRepo = new LgpdRopaRepository();
            $ropaId = $ropaRepo->create($formData);
            
            if ($ropaId) {
                // Criar Data Mapping automaticamente
                $dataMappingRepo = new LgpdDataMappingRepository();
                $mappingId = $dataMappingRepo->createFromRopa($ropaId);
                
                if ($mappingId) {
                    $_SESSION['msg'] = "<div class='alert alert-success' role='alert'>ROPA criada com sucesso! Data Mapping também foi criado automaticamente.</div>";
                } else {
                    $_SESSION['msg'] = "<div class='alert alert-warning' role='alert'>ROPA criada com sucesso! Data Mapping não foi criado automaticamente.</div>";
                }
                
                $urlRedirect = $_ENV['URL_ADM'] . "lgpd-ropa-view/$ropaId";
                header("Location: $urlRedirect");
                exit();
            } else {
                $_SESSION['msg'] = "<div class='alert alert-danger' role='alert'>Erro: ROPA não foi criada!</div>";
                $this->viewCreateFromInventory($inventoryId);
            }
        } else {
            $this->viewCreateFromInventory($inventoryId);
        }
    }
} 
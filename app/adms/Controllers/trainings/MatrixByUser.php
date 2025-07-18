<?php

namespace App\adms\Controllers\trainings;

use App\adms\Models\Repository\TrainingUsersRepository;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Models\Repository\DepartmentsRepository;
use App\adms\Models\Repository\PositionsRepository;
use App\adms\Models\Repository\TrainingsRepository;

class MatrixByUser
{
    private TrainingUsersRepository $trainingUsersRepo;
    private UsersRepository $usersRepo;
    private DepartmentsRepository $departmentsRepo;
    private PositionsRepository $positionsRepo;
    private TrainingsRepository $trainingsRepo;

    public function __construct()
    {
        $this->trainingUsersRepo = new TrainingUsersRepository();
        $this->usersRepo = new UsersRepository();
        $this->departmentsRepo = new DepartmentsRepository();
        $this->positionsRepo = new PositionsRepository();
        $this->trainingsRepo = new TrainingsRepository();
    }

    public function index(): void
    {
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], [10, 20, 50, 100]) ? (int)$_GET['per_page'] : 10;
        $offset = ($page - 1) * $perPage;
        $filters = [
            'colaborador' => $_GET['colaborador'] ?? null,
            'departamento' => $_GET['departamento'] ?? null,
            'cargo' => $_GET['cargo'] ?? null,
            'treinamento' => $_GET['treinamento'] ?? null,
        ];
        
        $matrixByUser = [];
        $total = 0;
        if (!empty($filters['treinamento'])) {
            // Se filtrou por um treinamento específico, traz todos os vinculados (cargo e individual)
            $matrixByUser = $this->trainingUsersRepo->getAllVinculadosPorTreinamento($filters['treinamento']);
            $total = count($matrixByUser);
            // Paginação manual
            $matrixByUser = array_slice($matrixByUser, $offset, $perPage);
        } else {
            // Comportamento padrão (apenas obrigatórios por cargo)
            $matrixByUser = $this->trainingUsersRepo->getMandatoryMatrixByUser($filters, $perPage, $offset);
            if (!is_array($matrixByUser)) {
                $matrixByUser = [];
            }
            $total = count($this->trainingUsersRepo->getMandatoryMatrixByUser($filters, 1000000, 0));
        }

        // Exportação - Buscar todos os dados sem paginação
        if (isset($_GET['export']) && in_array($_GET['export'], ['excel', 'pdf'])) {
            $exportData = [];
            if (!empty($filters['treinamento'])) {
                $exportData = $this->trainingUsersRepo->getAllVinculadosPorTreinamento($filters['treinamento']);
            } else {
                $exportData = $this->trainingUsersRepo->getMandatoryMatrixByUser($filters, 1000000, 0);
                if (!is_array($exportData)) {
                    $exportData = [];
                }
            }
            
            if ($_GET['export'] === 'excel') {
                $this->exportExcel($exportData);
            } else {
                $this->exportPdf($exportData);
            }
            return;
        }
        $pagination = \App\adms\Controllers\Services\PaginationService::generatePagination(
            $total,
            $perPage,
            $page,
            'matrix-by-user',
            array_merge($filters, ['per_page' => $perPage])
        );
        $data = [
            'title_head' => 'Matriz de Treinamentos por Colaborador',
            'matrixByUser' => $matrixByUser,
            'menu' => 'gestao_treinamentos',
            'buttonPermission' => [],
            'filters' => $filters,
            'listUsers' => $this->usersRepo->getAllUsersSelect(),
            'listDepartments' => $this->departmentsRepo->getAllDepartmentsSelect(),
            'listPositions' => $this->positionsRepo->getAllPositionsSelect(),
            'listTrainings' => $this->trainingsRepo->getAllTrainingsSelect(),
            'pagination' => $pagination,
            'per_page' => $perPage,
        ];
        
        $pageLayout = new \App\adms\Controllers\Services\PageLayoutService();
        $data = $pageLayout->configurePageElements($data);
        $loadView = new \App\adms\Views\Services\LoadViewService('adms/Views/trainings/matrixByUser', $data);
        $loadView->loadView();
    }

    private function exportExcel(array $matrix): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Cabeçalho
        $headers = ['ID', 'Nome', 'Departamento', 'Cargo', 'Treinamento Obrigatório', 'Código'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Dados
        $row = 2;
        foreach ($matrix as $item) {
            $sheet->setCellValue('A' . $row, $item['id'] ?? $item['user_id'] ?? '-');
            $sheet->setCellValue('B' . $row, $item['user_name'] ?? $item['name'] ?? '');
            $sheet->setCellValue('C' . $row, $item['department'] ?? $item['department_nome'] ?? '');
            $sheet->setCellValue('D' . $row, $item['position'] ?? $item['cargo_nome'] ?? '');
            $sheet->setCellValue('E' . $row, $item['training_name'] ?? $item['treinamento_nome'] ?? '');
            $sheet->setCellValue('F' . $row, $item['codigo'] ?? '');
            $row++;
        }
        
        // Ajustar largura das colunas
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="matriz_treinamentos_por_colaborador.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function exportPdf(array $matrix): void
    {
        // Montar HTML da tabela
        $html = '<h2 style="text-align:center;">Matriz de Treinamentos por Colaborador</h2>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%" style="font-size:10px; border-collapse:collapse;">';
        $html .= '<thead><tr style="background:#f0f0f0;">';
        $html .= '<th>ID</th><th>Nome</th><th>Departamento</th><th>Cargo</th><th>Treinamento Obrigatório</th><th>Código</th>';
        $html .= '</tr></thead><tbody>';
        
        foreach ($matrix as $item) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($item['id'] ?? $item['user_id'] ?? '-') . '</td>';
            $html .= '<td>' . htmlspecialchars($item['user_name'] ?? $item['name'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($item['department'] ?? $item['department_nome'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($item['position'] ?? $item['cargo_nome'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($item['training_name'] ?? $item['treinamento_nome'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($item['codigo'] ?? '') . '</td>';
            $html .= '</tr>';
        }
        
        if (empty($matrix)) {
            $html .= '<tr><td colspan="6" style="text-align:center; color:#888;">Nenhum vínculo obrigatório encontrado.</td></tr>';
        }
        
        $html .= '</tbody></table>';

        // Gerar PDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('matriz_treinamentos_por_colaborador.pdf', ['Attachment' => true]);
        exit;
    }
} 
<?php

namespace App\adms\Controllers\trainings;

use App\adms\Models\Repository\TrainingUsersRepository;
use App\adms\Models\Repository\UsersRepository;
use App\adms\Models\Repository\TrainingsRepository;
use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Views\Services\LoadViewService;

class CompletedTrainingsMatrix
{
    private TrainingUsersRepository $trainingUsersRepo;
    private UsersRepository $usersRepo;
    private TrainingsRepository $trainingsRepo;

    public function __construct()
    {
        $this->trainingUsersRepo = new TrainingUsersRepository();
        $this->usersRepo = new UsersRepository();
        $this->trainingsRepo = new TrainingsRepository();
    }

    public function index(): void
    {
        $filters = [
            'colaborador' => $_GET['colaborador'] ?? null,
            'treinamento' => $_GET['treinamento'] ?? null,
            'mes' => $_GET['mes'] ?? null,
            'ano' => $_GET['ano'] ?? null,
            'sort' => $_GET['sort'] ?? null,
            'order' => $_GET['order'] ?? null,
        ];
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) && is_numeric($_GET['per_page']) ? (int)$_GET['per_page'] : 20;
        $matrixData = $this->trainingUsersRepo->getCompletedTrainingsMatrixPaginated($filters, $page, $perPage);
        $matrix = $matrixData['data'];
        $total = $matrixData['total'];
        $summary = $this->trainingUsersRepo->getCompletedTrainingsSummary($filters);
        // Exportação
        if (isset($_GET['export']) && $_GET['export'] === 'excel') {
            $this->exportExcel($matrix);
            return;
        }
        if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
            $this->exportPdf($matrix);
            return;
        }
        $data = [
            'title_head' => 'Matriz de Treinamentos Realizados',
            'menu' => 'completed-trainings-matrix',
            'buttonPermission' => [],
            'filters' => $filters,
            'matrix' => $matrix,
            'summary' => $summary,
            'listUsers' => $this->usersRepo->getAllUsersSelect(),
            'listTrainings' => $this->trainingsRepo->getAllTrainingsSelect(),
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
        ];
        $pageLayout = new PageLayoutService();
        $data = $pageLayout->configurePageElements($data);
        $loadView = new LoadViewService('adms/Views/trainings/completedTrainingsMatrix', $data);
        $loadView->loadView();
    }

    private function exportExcel(array $matrix): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Cabeçalho
        $headers = ['Colaborador', 'Treinamento', 'Código', 'Data Realização', 'Instrutor', 'Nota', 'Observações'];
        $sheet->fromArray($headers, null, 'A1');
        // Dados
        $row = 2;
        foreach ($matrix as $item) {
            $sheet->setCellValue('A' . $row, $item['user_name']);
            $sheet->setCellValue('B' . $row, $item['training_name']);
            $sheet->setCellValue('C' . $row, $item['training_code']);
            $sheet->setCellValue('D' . $row, $item['data_realizacao'] ? date('d/m/Y', strtotime($item['data_realizacao'])) : '-');
            $sheet->setCellValue('E' . $row, $item['instrutor_nome'] ?? '-');
            $sheet->setCellValue('F' . $row, $item['nota'] ?? '-');
            $sheet->setCellValue('G' . $row, $item['observacoes'] ?? '-');
            $row++;
        }
        // Ajustar largura das colunas
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // Download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="matriz_treinamentos_realizados.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function exportPdf(array $matrix): void
    {
        // Montar HTML da tabela
        $html = '<h2 style="text-align:center;">Matriz de Treinamentos Realizados</h2>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%" style="font-size:12px; border-collapse:collapse;">';
        $html .= '<thead><tr style="background:#f0f0f0;">';
        $html .= '<th>Colaborador</th><th>Treinamento</th><th>Código</th><th>Data Realização</th><th>Instrutor</th><th>Nota</th><th>Observações</th>';
        $html .= '</tr></thead><tbody>';
        foreach ($matrix as $item) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($item['user_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['training_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['training_code']) . '</td>';
            $html .= '<td>' . ($item['data_realizacao'] ? date('d/m/Y', strtotime($item['data_realizacao'])) : '-') . '</td>';
            $html .= '<td>' . htmlspecialchars($item['instrutor_nome'] ?? '-') . '</td>';
            $html .= '<td>' . htmlspecialchars($item['nota'] ?? '-') . '</td>';
            $html .= '<td>' . htmlspecialchars($item['observacoes'] ?? '-') . '</td>';
            $html .= '</tr>';
        }
        if (empty($matrix)) {
            $html .= '<tr><td colspan="7" style="text-align:center; color:#888;">Nenhum treinamento realizado encontrado.</td></tr>';
        }
        $html .= '</tbody></table>';

        // Gerar PDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('matriz_treinamentos_realizados.pdf', ['Attachment' => true]);
        exit;
    }
} 
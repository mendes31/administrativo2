<?php

namespace App\adms\Controllers\logs;

use App\adms\Models\Repository\LogAlteracoesRepository;
use Dompdf\Dompdf;

class ExportLogPdf
{
    public function index(): void
    {
        $filtros = $this->getFiltros();
        $repo = new LogAlteracoesRepository();
        $logs = $repo->getAll(1, 10000, $filtros);

        // Montar HTML da tabela
        $html = '<h2 style="text-align:center;">Log de Modificações</h2>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%" style="font-size:12px; border-collapse:collapse;">';
        $html .= '<thead><tr style="background:#f0f0f0;">';
        $html .= '<th>ID</th><th>Tabela</th><th>ID Objeto</th><th>Identificador</th><th>Usuário</th><th>Data/Hora</th><th>Tipo</th><th>IP</th><th>User Agent</th>';
        $html .= '</tr></thead><tbody>';
        foreach ($logs as $log) {
            $html .= '<tr>';
            $html .= '<td>' . $log['id'] . '</td>';
            $html .= '<td>' . htmlspecialchars($log['tabela']) . '</td>';
            $html .= '<td>' . $log['objeto_id'] . '</td>';
            $html .= '<td>' . htmlspecialchars($log['identificador']) . '</td>';
            $html .= '<td>' . htmlspecialchars($log['usuario_nome'] ?: $log['usuario_id']) . '</td>';
            $html .= '<td>' . date('d/m/Y H:i:s', strtotime($log['data_alteracao'])) . '</td>';
            $html .= '<td>' . htmlspecialchars($log['tipo_operacao']) . '</td>';
            $html .= '<td>' . htmlspecialchars($log['ip']) . '</td>';
            $html .= '<td>' . htmlspecialchars($log['user_agent']) . '</td>';
            $html .= '</tr>';
        }
        if (empty($logs)) {
            $html .= '<tr><td colspan="9" style="text-align:center; color:#888;">Nenhum log encontrado.</td></tr>';
        }
        $html .= '</tbody></table>';

        // Gerar PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('logs_alteracoes_' . date('Y-m-d_H-i-s') . '.pdf', ['Attachment' => true]);
        exit;
    }
    
    private function getFiltros(): array
    {
        return [
            'tabela' => $_GET['tabela'] ?? '',
            'objeto_id' => $_GET['objeto_id'] ?? '',
            'identificador' => $_GET['identificador'] ?? '',
            'usuario_nome' => $_GET['usuario_nome'] ?? '',
            'tipo' => $_GET['tipo'] ?? '',
            'data_inicio' => $_GET['data_inicio'] ?? '',
            'data_fim' => $_GET['data_fim'] ?? '',
        ];
    }
} 
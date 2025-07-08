<?php
use App\adms\Helpers\FormatHelper;

// Função para determinar o aproveitamento
function getPerformanceStatus($grade) {
    if ($grade === null || $grade === '' || $grade === '-') {
        return ['label' => '', 'class' => ''];
    } elseif ($grade <= 5) {
        return ['label' => 'Reprovado', 'class' => 'performance-reprovado'];
    } elseif ($grade < 7) {
        return ['label' => 'Exame', 'class' => 'performance-exame'];
    } else {
        return ['label' => 'Aprovado', 'class' => 'performance-aprovado'];
    }
}
$performanceFilter = $_GET['performance'] ?? '';
?>
<style>
.performance-reprovado {
    background-color: #ffcccc !important;
    color: #b20000 !important;
    font-weight: bold !important;
}
.performance-exame {
    background-color: #fff3cd !important;
    color: #b8860b !important;
    font-weight: bold !important;
}
.performance-aprovado {
    background-color: #d4edda !important;
    color: #155724 !important;
    font-weight: bold !important;
}
</style>
<div class="container-fluid px-4">
    <div class="mb-1 d-flex align-items-center justify-content-between" style="min-height:48px;">
        <h2 class="mt-3 mb-0">Matriz de Treinamentos Realizados</h2>
        <ol class="breadcrumb mb-0 mt-3 ms-auto bg-transparent p-0">
            <li class="breadcrumb-item"><a href="<?= $_ENV['URL_ADM'] ?>dashboard" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active">Matriz de Treinamentos Realizados</li>
        </ol>
    </div>
    <div class="row mb-4 g-3">
        <div class="col-md-2">
            <div class="card border-warning h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-2 text-warning"><i class="fas fa-clock"></i></div>
                    <div class="fw-bold fs-4 mb-1"><?= $this->data['summary']['pendente'] ?? 0 ?></div>
                    <div class="text-muted">Pendentes</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-success h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-2 text-success"><i class="fas fa-check-circle"></i></div>
                    <div class="fw-bold fs-4 mb-1"><?= $this->data['summary']['concluido'] ?? 0 ?></div>
                    <div class="text-muted">Concluídos</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-danger h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-2 text-danger"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="fw-bold fs-4 mb-1"><?= $this->data['summary']['vencido'] ?? 0 ?></div>
                    <div class="text-muted">Vencidos</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-info h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-2 text-info"><i class="fas fa-calendar-alt"></i></div>
                    <div class="fw-bold fs-4 mb-1"><?= $this->data['summary']['agendado'] ?? 0 ?></div>
                    <div class="text-muted">Agendados</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-warning h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-2 text-warning"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="fw-bold fs-4 mb-1"><?= $this->data['summary']['proximo_vencimento'] ?? 0 ?></div>
                    <div class="text-muted">Próximo Vencimento</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-primary h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-2 text-primary"><i class="fas fa-users"></i></div>
                    <div class="fw-bold fs-4 mb-1"><?= $this->data['summary']['total'] ?? 0 ?></div>
                    <div class="text-muted">Total</div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-4 border-light shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Treinamentos Realizados por Colaborador</h5>
            <div>
                <a href="<?= $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '&' : '?') ?>export=excel" class="btn btn-success btn-sm me-2"><i class="fas fa-file-excel me-1"></i>Exportar Excel</a>
                <a href="<?= $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '&' : '?') ?>export=pdf" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf me-1"></i>Exportar PDF</a>
            </div>
        </div>
        <div class="card-body pt-2">
            <form method="GET" class="row g-3 align-items-end mb-0">
                <div class="col-md-3">
                    <label for="colaborador" class="form-label mb-1">Colaborador</label>
                    <select name="colaborador" id="colaborador" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (($this->data['listUsers'] ?? []) as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= ($this->data['filters']['colaborador'] ?? '') == $user['id'] ? 'selected' : '' ?>><?= $user['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="treinamento" class="form-label mb-1">Treinamento</label>
                    <select name="treinamento" id="treinamento" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (($this->data['listTrainings'] ?? []) as $trein): ?>
                            <option value="<?= $trein['id'] ?>" <?= ($this->data['filters']['treinamento'] ?? '') == $trein['id'] ? 'selected' : '' ?>><?= $trein['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="mes" class="form-label mb-1">Mês</label>
                    <select name="mes" id="mes" class="form-select">
                        <option value="">Todos</option>
                        <?php
                        $meses = [
                            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
                        ];
                        foreach ($meses as $num => $nome): ?>
                            <option value="<?= $num ?>" <?= ($this->data['filters']['mes'] ?? '') == $num ? 'selected' : '' ?>><?= $nome ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="ano" class="form-label mb-1">Ano</label>
                    <select name="ano" id="ano" class="form-select">
                        <option value="">Todos</option>
                        <?php
                        $anoAtual = date('Y');
                        for ($ano = $anoAtual; $ano >= $anoAtual - 5; $ano--): ?>
                            <option value="<?= $ano ?>" <?= ($this->data['filters']['ano'] ?? '') == $ano ? 'selected' : '' ?>><?= $ano ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="performance" class="form-label mb-1">Aproveitamento</label>
                    <select name="performance" id="performance" class="form-select">
                        <option value="">Todos</option>
                        <option value="aprovado" <?= $performanceFilter === 'aprovado' ? 'selected' : '' ?>>Aprovado</option>
                        <option value="exame" <?= $performanceFilter === 'exame' ? 'selected' : '' ?>>Exame</option>
                        <option value="reprovado" <?= $performanceFilter === 'reprovado' ? 'selected' : '' ?>>Reprovado</option>
                        <option value="vazio" <?= $performanceFilter === 'vazio' ? 'selected' : '' ?>>Vazio</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary btn-lg px-4"><i class="fas fa-search me-2"></i>Filtrar</button>
                    <a href="<?= $_ENV['URL_ADM'] ?>completed-trainings-matrix" class="btn btn-secondary btn-lg px-4"><i class="fas fa-times me-2"></i>Limpar</a>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <label for="per_page" class="form-label mb-1 me-2">Exibir</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <?php foreach ([10, 20, 50, 100] as $opt): ?>
                            <option value="<?= $opt ?>" <?= ($this->data['per_page'] ?? 20) == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="ms-2">por página</span>
                </div>
            </form>
        </div>
    </div>
    <div class="table-responsive" style="max-height: 70vh;">
        <table class="table table-striped table-hover" style="table-layout: fixed; width: 100%; min-width:900px;">
            <thead style="position:sticky;top:0;z-index:1;background:#f8f9fa;">
                <tr>
                    <?php
                    // Parâmetros atuais
                    $params = $_GET;
                    $sort = $_GET['sort'] ?? '';
                    $order = $_GET['order'] ?? 'asc';
                    function sort_link($col, $label, $sort, $order, $params) {
                        $params['sort'] = $col;
                        $params['order'] = ($sort === $col && $order === 'asc') ? 'desc' : 'asc';
                        $icon = '';
                        if ($sort === $col) {
                            $icon = $order === 'asc' ? ' <i class="fas fa-sort-up"></i>' : ' <i class="fas fa-sort-down"></i>';
                        }
                        $url = '?' . http_build_query($params);
                        return '<a href="' . $url . '" class="text-decoration-none text-dark">' . $label . $icon . '</a>';
                    }
                    ?>
                    <th><?= sort_link('user_name', 'Colaborador', $sort, $order, $params) ?></th>
                    <th><?= sort_link('training_name', 'Treinamento', $sort, $order, $params) ?></th>
                    <th><?= sort_link('training_code', 'Código', $sort, $order, $params) ?></th>
                    <th><?= sort_link('data_realizacao', 'Data Realização', $sort, $order, $params) ?></th>
                    <th><?= sort_link('instrutor_nome', 'Instrutor', $sort, $order, $params) ?></th>
                    <th><?= sort_link('nota', 'Nota', $sort, $order, $params) ?></th>
                    <th>Aproveitamento</th>
                    <th><?= sort_link('observacoes', 'Observações', $sort, $order, $params) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($this->data['matrix'])): ?>
                    <?php foreach ($this->data['matrix'] as $item):
                        $performance = getPerformanceStatus($item['nota'] ?? null);
                        // Filtro
                        $filterMatch = false;
                        if ($performanceFilter === '' ||
                            ($performanceFilter === 'aprovado' && $performance['label'] === 'Aprovado') ||
                            ($performanceFilter === 'exame' && $performance['label'] === 'Exame') ||
                            ($performanceFilter === 'reprovado' && $performance['label'] === 'Reprovado') ||
                            ($performanceFilter === 'vazio' && $performance['label'] === '')) {
                            $filterMatch = true;
                        }
                        if (!$filterMatch) continue;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['user_name']) ?></td>
                            <td><?= htmlspecialchars($item['training_name']) ?></td>
                            <td><?= htmlspecialchars($item['training_code']) ?></td>
                            <td>
                                <?php if (!empty($item['data_realizacao'])): ?>
                                    <?= (new DateTime($item['data_realizacao']))->format('d/m/Y H:i') ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['instrutor_nome'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($item['nota'] ?? '-') ?></td>
                            <td class="<?= $performance['class'] ?>"><?= $performance['label'] ?></td>
                            <td><?= htmlspecialchars($item['observacoes'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center text-muted">Nenhum treinamento realizado encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
    // Paginação
    $page = $this->data['page'] ?? 1;
    $perPage = $this->data['per_page'] ?? 20;
    $total = $this->data['total'] ?? 0;
    $totalPages = max(1, ceil($total / $perPage));
    $params = $_GET;
    function pageUrl($n, $params) {
        $params['page'] = $n;
        return '?' . http_build_query($params);
    }
    ?>
    <nav aria-label="Navegação de páginas">
        <ul class="pagination justify-content-end mt-3">
            <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                <a class="page-link" href="<?= pageUrl(1, $params) ?>">&laquo;</a>
            </li>
            <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                <a class="page-link" href="<?= pageUrl($page-1, $params) ?>">Anterior</a>
            </li>
            <li class="page-item active"><span class="page-link">Página <?= $page ?> de <?= $totalPages ?></span></li>
            <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
                <a class="page-link" href="<?= pageUrl($page+1, $params) ?>">Próxima</a>
            </li>
            <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
                <a class="page-link" href="<?= pageUrl($totalPages, $params) ?>">&raquo;</a>
            </li>
        </ul>
    </nav>
</div> 
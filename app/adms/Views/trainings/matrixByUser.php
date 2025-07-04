<?php
// View: Matriz de Treinamentos por Colaborador
?>
<div class="container-fluid px-4">
    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Matriz de Treinamentos por Colaborador</h2>
        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item"><a href="<?= $_ENV['URL_ADM'] ?>dashboard" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item">Matriz de Treinamentos</li>
        </ol>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 border-light shadow">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="colaborador" class="form-label">Colaborador</label>
                    <select name="colaborador" id="colaborador" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (($this->data['listUsers'] ?? []) as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= ($this->data['filters']['colaborador'] ?? '') == $user['id'] ? 'selected' : '' ?>><?= $user['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="departamento" class="form-label">Departamento</label>
                    <select name="departamento" id="departamento" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (($this->data['listDepartments'] ?? []) as $dep): ?>
                            <option value="<?= $dep['id'] ?>" <?= ($this->data['filters']['departamento'] ?? '') == $dep['id'] ? 'selected' : '' ?>><?= $dep['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="cargo" class="form-label">Cargo</label>
                    <select name="cargo" id="cargo" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (($this->data['listPositions'] ?? []) as $pos): ?>
                            <option value="<?= $pos['id'] ?>" <?= ($this->data['filters']['cargo'] ?? '') == $pos['id'] ? 'selected' : '' ?>><?= $pos['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="treinamento" class="form-label">Treinamento</label>
                    <select name="treinamento" id="treinamento" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (($this->data['listTrainings'] ?? []) as $trein): ?>
                            <option value="<?= $trein['id'] ?>" <?= ($this->data['filters']['treinamento'] ?? '') == $trein['id'] ? 'selected' : '' ?>><?= $trein['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 d-flex gap-2 justify-content-between align-items-center">
                    <div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-2"></i>Filtrar</button>
                        <a href="<?= $_ENV['URL_ADM'] ?>matrix-by-user" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Limpar</a>
                    </div>
                    <div>
                        <label for="per_page" class="mb-0">Mostrar</label>
                        <select name="per_page" id="per_page" class="form-select form-select-sm d-inline-block w-auto ms-1" onchange="this.form.submit()">
                            <?php foreach ([10, 20, 50, 100] as $opt): ?>
                                <option value="<?= $opt ?>" <?= ($this->data['per_page'] ?? 10) == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="ms-1">registros</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4 border-light shadow">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Treinamentos Obrigatórios por Colaborador</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-end align-items-center mb-2 flex-wrap gap-2">
                <!-- seletor removido daqui -->
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="matrixByUserTable">
                    <thead>
                        <tr>
                            <th>Colaborador</th>
                            <th>Departamento</th>
                            <th>Cargo</th>
                            <th>Treinamento Obrigatório</th>
                            <th>Código</th>
                            <th>Reciclagem</th>
                            <th>Validade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($this->data['matrixByUser'])): ?>
                            <?php foreach ($this->data['matrixByUser'] as $item): ?>
                                <tr>
                                    <td>
                                        <a href="<?= $_ENV['URL_ADM'] ?>training-history/<?= $item['user_id'] ?>-<?= $item['training_id'] ?>" 
                                           class="text-decoration-none" title="Ver histórico de reciclagem">
                                            <?= htmlspecialchars($item['user_name']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($item['department']) ?></td>
                                    <td><?= htmlspecialchars($item['position']) ?></td>
                                    <td>
                                        <a href="<?= $_ENV['URL_ADM'] ?>training-history/<?= $item['user_id'] ?>-<?= $item['training_id'] ?>" 
                                           class="text-decoration-none" title="Ver histórico de reciclagem">
                                            <?= htmlspecialchars($item['training_name']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($item['codigo']) ?></td>
                                    <td>
                                        <?php if ($item['reciclagem'] && $item['reciclagem_periodo']): ?>
                                            <?= $item['reciclagem_periodo'] ?> meses
                                        <?php else: ?>
                                            <span class="text-muted">Não exige</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['validade']): ?>
                                            <?= htmlspecialchars($item['validade']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center text-muted">Nenhum vínculo obrigatório encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div class="text-muted small">
                    <?php
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $perPage = $this->data['per_page'] ?? 10;
                    $total = $this->data['pagination']['total'] ?? count($this->data['matrixByUser']);
                    $from = ($total > 0) ? (($page - 1) * $perPage + 1) : 0;
                    $to = min($from + count($this->data['matrixByUser']) - 1, $total);
                    ?>
                    Mostrando <?= $from ?> até <?= $to ?> de <?= $total ?> registro(s)
                </div>
                <?php if (!empty($this->data['pagination']['html'])): ?>
                    <div>
                        <?= $this->data['pagination']['html'] ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 
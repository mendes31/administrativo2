<?php
if (!isset($this->data['matrixByUser']) || !is_array($this->data['matrixByUser'])) {
    $this->data['matrixByUser'] = [];
}
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
                    <div class="d-flex align-items-center gap-2">
                        <div>
                            <label for="per_page" class="mb-0">Mostrar</label>
                            <select name="per_page" id="per_page" class="form-select form-select-sm d-inline-block w-auto ms-1" onchange="this.form.submit()">
                                <?php foreach ([10, 20, 50, 100] as $opt): ?>
                                    <option value="<?= $opt ?>" <?= ($this->data['per_page'] ?? 10) == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="ms-1">registros</span>
                        </div>
                        <div class="ms-3">
                            <a href="<?= $_ENV['URL_ADM'] ?>matrix-by-user?<?= http_build_query(array_merge($_GET, ['export' => 'excel'])) ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i>Exportar Excel
                            </a>
                            <a href="<?= $_ENV['URL_ADM'] ?>matrix-by-user?<?= http_build_query(array_merge($_GET, ['export' => 'pdf'])) ?>" class="btn btn-danger btn-sm">
                                <i class="fas fa-file-pdf me-1"></i>Exportar PDF
                            </a>
                        </div>
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
            <!-- Tabela Desktop -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-striped table-hover" id="matrixByUserTable" style="table-layout: fixed; width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th class="col-nome">Nome</th>
                            <th>Departamento</th>
                            <th>Cargo</th>
                            <th>Treinamento Obrigatório</th>
                            <th>Código</th>
                            <!-- <th>Reciclagem</th> -->
                            <!-- <th>Validade</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($this->data['matrixByUser'])): ?>
                            <?php foreach ($this->data['matrixByUser'] as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?? $item['user_id'] ?? '-' ?></td>
                                    <td class="col-nome">
                                        <a href="<?= $_ENV['URL_ADM'] ?>training-history/<?= $item['user_id'] ?? $item['id'] ?>-<?= $item['training_id'] ?>" 
                                           class="text-decoration-none" title="Ver histórico de reciclagem">
                                            <?= htmlspecialchars($item['user_name'] ?? $item['name'] ?? '') ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($item['department'] ?? $item['department_nome'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($item['position'] ?? $item['cargo_nome'] ?? '') ?></td>
                                    <td>
                                        <a href="<?= $_ENV['URL_ADM'] ?>training-history/<?= $item['user_id'] ?? $item['id'] ?>-<?= $item['training_id'] ?>" 
                                           class="text-decoration-none" title="Ver histórico de reciclagem">
                                            <?= htmlspecialchars($item['training_name'] ?? $item['treinamento_nome'] ?? '') ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($item['codigo'] ?? '') ?></td>
                                    <!-- <td>
                                        <?php if (($item['reciclagem'] ?? false) && ($item['reciclagem_periodo'] ?? false)): ?>
                                            <?= $item['reciclagem_periodo'] ?> meses
                                        <?php else: ?>
                                            <span class="text-muted">Não exige</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['validade'] ?? false): ?>
                                            <?= htmlspecialchars($item['validade']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td> -->
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted">Nenhum vínculo obrigatório encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- CARDS MOBILE -->
            <div class="d-block d-md-none">
                <?php if (!empty($this->data['matrixByUser'])): ?>
                    <?php foreach ($this->data['matrixByUser'] as $i => $item): ?>
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title mb-1"><b><?= htmlspecialchars($item['user_name'] ?? $item['name'] ?? '') ?></b></h5>
                                        <div class="mb-1"><b>ID:</b> <?= htmlspecialchars($item['id'] ?? $item['user_id'] ?? '-') ?></div>
                                        <div class="mb-1"><b>Treinamento:</b> <?= htmlspecialchars($item['training_name'] ?? $item['treinamento_nome'] ?? '') ?></div>
                                        <div class="mb-1"><b>Status:</b> <?= ($item['validade'] ?? false) ? '<span class=\'badge bg-success\'>Válido</span>' : '<span class=\'badge bg-secondary\'>-</span>' ?></div>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#cardMatrixDetails<?= $i ?>" aria-expanded="false" aria-controls="cardMatrixDetails<?= $i ?>">Ver mais</button>
                                </div>
                                <div class="collapse mt-2" id="cardMatrixDetails<?= $i ?>">
                                    <div><b>Departamento:</b> <?= htmlspecialchars($item['department'] ?? $item['department_nome'] ?? '') ?></div>
                                    <div><b>Cargo:</b> <?= htmlspecialchars($item['position'] ?? $item['cargo_nome'] ?? '') ?></div>
                                    <div><b>Código:</b> <?= htmlspecialchars($item['codigo'] ?? '') ?></div>
                                    <!-- <div><b>Reciclagem:</b> <?php if (($item['reciclagem'] ?? false) && ($item['reciclagem_periodo'] ?? false)): ?><?= $item['reciclagem_periodo'] ?> meses<?php else: ?><span class="text-muted">Não exige</span><?php endif; ?></div>
                                    <div><b>Validade:</b> <?= htmlspecialchars($item['validade'] ?? '-') ?></div> -->
                                    <div class="mt-2">
                                        <a href="<?= $_ENV['URL_ADM'] ?>training-history/<?= $item['user_id'] ?? $item['id'] ?>-<?= $item['training_id'] ?>" class="btn btn-info btn-sm me-1 mb-1" title="Ver histórico de reciclagem"><i class="fas fa-history"></i> Histórico</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-danger" role="alert">Nenhum vínculo obrigatório encontrado.</div>
                <?php endif; ?>

                <!-- Paginação e informações abaixo dos cards no mobile -->
                <div class="d-flex d-md-none flex-column align-items-center w-100 mt-2">
                    <div class="text-secondary small w-100 text-center mb-1">
                        <?php
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $perPage = $this->data['per_page'] ?? 10;
                        $total = $this->data['pagination']['total'] ?? count($this->data['matrixByUser']);
                        $from = ($total > 0) ? (($page - 1) * $perPage + 1) : 0;
                        $to = min($from + count($this->data['matrixByUser']) - 1, $total);
                        ?>
                        Mostrando <?= $from ?> até <?= $to ?> de <?= $total ?> registro(s)
                    </div>
                    <div class="w-100 d-flex justify-content-center">
                        <?= $this->data['pagination']['html'] ?? '' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
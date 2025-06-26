<?php
// Filtros
?>
<div class="container-fluid px-4">
    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Status de Treinamentos por Colaborador</h2>
        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item"><a href="<?= $_ENV['URL_ADM'] ?>dashboard" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item">Status de Treinamentos</li>
        </ol>
    </div>
    <div class="card mb-4 border-light shadow">
        <div class="card-header hstack gap-2">
            <span>Listar</span>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label for="colaborador" class="form-label">Colaborador</label>
                    <select name="colaborador" id="colaborador" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (($this->data['listUsers'] ?? []) as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= ($this->data['filters']['colaborador'] ?? '') == $user['id'] ? 'selected' : '' ?>><?= $user['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="departamento" class="form-label">Departamento</label>
                    <select name="departamento" id="departamento" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (($this->data['listDepartments'] ?? []) as $dep): ?>
                            <option value="<?= $dep['id'] ?>" <?= ($this->data['filters']['departamento'] ?? '') == $dep['id'] ? 'selected' : '' ?>><?= $dep['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="cargo" class="form-label">Cargo</label>
                    <select name="cargo" id="cargo" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (($this->data['listPositions'] ?? []) as $pos): ?>
                            <option value="<?= $pos['id'] ?>" <?= ($this->data['filters']['cargo'] ?? '') == $pos['id'] ? 'selected' : '' ?>><?= $pos['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="treinamento" class="form-label">Treinamento</label>
                    <select name="treinamento" id="treinamento" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (($this->data['listTrainings'] ?? []) as $trein): ?>
                            <option value="<?= $trein['id'] ?>" <?= ($this->data['filters']['treinamento'] ?? '') == $trein['id'] ? 'selected' : '' ?>><?= $trein['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendente" <?= ($this->data['filters']['status'] ?? '') == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                        <option value="concluido" <?= ($this->data['filters']['status'] ?? '') == 'concluido' ? 'selected' : '' ?>>Concluído</option>
                        <option value="vencido" <?= ($this->data['filters']['status'] ?? '') == 'vencido' ? 'selected' : '' ?>>Vencido</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <a href="<?= $_ENV['URL_ADM'] ?>list-training-status" class="btn btn-secondary w-100">Limpar</a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Colaborador</th>
                            <th>Departamento</th>
                            <th>Cargo</th>
                            <th>Treinamento</th>
                            <th>Validade</th>
                            <th>Status</th>
                            <th>Data Realização</th>
                            <th>Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($this->data['trainingStatus'])): ?>
                            <?php foreach ($this->data['trainingStatus'] as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                                    <td><?= htmlspecialchars($row['department']) ?></td>
                                    <td><?= htmlspecialchars($row['position']) ?></td>
                                    <td><?= htmlspecialchars($row['training_name']) ?></td>
                                    <td><?= htmlspecialchars($row['validade']) ?></td>
                                    <td>
                                        <?php
                                        $status = $row['status'] ?? 'pendente';
                                        $badge = 'secondary';
                                        if ($status == 'concluido') $badge = 'success';
                                        elseif ($status == 'pendente') $badge = 'warning';
                                        elseif ($status == 'vencido') $badge = 'danger';
                                        ?>
                                        <span class="badge bg-<?= $badge ?> text-uppercase"><?= $status ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($row['data_realizacao'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['nota'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center">Nenhum registro encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 
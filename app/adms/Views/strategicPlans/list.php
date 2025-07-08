<?php
// Cabeçalho já incluso pelo controller
?>
<div class="container-fluid">
    <div class="card mt-4">
        <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-project-diagram"></i> Listar Planos Estratégicos</h4>
            <a href="/adms/create-strategic-plan" class="btn btn-success"><i class="fas fa-plus"></i> Cadastrar</a>
        </div>
        <div class="card-body pb-0">
            <form class="row g-2 mb-3 align-items-end" method="get" action="">
                <div class="col-md-4">
                    <input type="text" name="titulo" class="form-control" placeholder="Buscar por título" value="<?= htmlspecialchars($criteria['titulo'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" name="departamento" class="form-control" placeholder="Buscar por departamento" value="<?= htmlspecialchars($criteria['departamento'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" name="responsavel" class="form-control" placeholder="Buscar por responsável" value="<?= htmlspecialchars($criteria['responsavel'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Status</option>
                        <option value="Não iniciado" <?= (isset($criteria['status']) && $criteria['status'] == 'Não iniciado') ? 'selected' : '' ?>>Não iniciado</option>
                        <option value="Em andamento" <?= (isset($criteria['status']) && $criteria['status'] == 'Em andamento') ? 'selected' : '' ?>>Em andamento</option>
                        <option value="Concluído" <?= (isset($criteria['status']) && $criteria['status'] == 'Concluído') ? 'selected' : '' ?>>Concluído</option>
                        <option value="Atrasado" <?= (isset($criteria['status']) && $criteria['status'] == 'Atrasado') ? 'selected' : '' ?>>Atrasado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="per_page" class="form-select">
                        <?php $pp = $per_page ?? 20; ?>
                        <option value="10" <?= $pp == 10 ? 'selected' : '' ?>>Exibir 10</option>
                        <option value="20" <?= $pp == 20 ? 'selected' : '' ?>>Exibir 20</option>
                        <option value="50" <?= $pp == 50 ? 'selected' : '' ?>>Exibir 50</option>
                        <option value="100" <?= $pp == 100 ? 'selected' : '' ?>>Exibir 100</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
                <div class="col-md-1 d-grid">
                    <a href="?" class="btn btn-secondary">Limpar Filtros</a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Título</th>
                            <th>Departamento</th>
                            <th>Responsável</th>
                            <th>Período</th>
                            <th>Status</th>
                            <th style="width: 140px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($plans)) : ?>
                            <?php foreach ($plans as $plan) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($plan['id']) ?></td>
                                    <td><?= htmlspecialchars($plan['title']) ?></td>
                                    <td><?= htmlspecialchars($plan['department_name'] ?? $plan['department_id'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($plan['responsible_name'] ?? $plan['responsible_id'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($plan['start_date']) ?> a <?= htmlspecialchars($plan['end_date']) ?></td>
                                    <td>
                                        <?php
                                        $status = $plan['status'];
                                        $badge = 'secondary';
                                        if ($status === 'Concluído') $badge = 'success';
                                        elseif ($status === 'Em andamento') $badge = 'primary';
                                        elseif ($status === 'Atrasado') $badge = 'danger';
                                        elseif ($status === 'Não iniciado') $badge = 'warning';
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($status) ?></span>
                                    </td>
                                    <td>
                                        <a href="/adms/view-strategic-plan/<?= $plan['id'] ?>" class="btn btn-sm btn-info" title="Visualizar"><i class="fas fa-eye"></i></a>
                                        <a href="/adms/edit-strategic-plan/<?= $plan['id'] ?>" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                                        <a href="/adms/delete-strategic-plan-/<?= $plan['id'] ?>" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este plano?');"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="7" class="text-center">Nenhum plano encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Paginação customizada -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <?php if (!empty($pagination['showing'])): ?>
                        <?= $pagination['showing'] ?>
                    <?php endif; ?>
                </div>
                <nav>
                    <?php if (!empty($pagination['links'])): ?>
                        <ul class="pagination mb-0">
                            <?= $pagination['links'] ?>
                        </ul>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </div>
</div>
<?php // Rodapé já incluso pelo controller ?> 
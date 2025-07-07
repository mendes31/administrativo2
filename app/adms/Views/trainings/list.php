<?php
use App\adms\Helpers\FormatHelper;
?>
<div class="container-fluid px-4">
    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Treinamentos</h2>
        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">Treinamentos</li>
        </ol>
    </div>
    
    <div class="card mb-4 border-light shadow">
        <div class="card-header hstack gap-2">
            <span><i class="fas fa-graduation-cap me-2"></i>Listar Treinamentos</span>
            <span class="ms-auto d-sm-flex flex-row">
                <a href="<?php echo $_ENV['URL_ADM']; ?>create-training" class="btn btn-success btn-sm me-1 mb-1">
                    <i class="fa-solid fa-plus"></i> Cadastrar
                </a>
                <a href="<?php echo $_ENV['URL_ADM']; ?>training-kpi-dashboard" class="btn btn-primary btn-sm me-1 mb-1">
                    <i class="fas fa-chart-line"></i> KPIs
                </a>
                <a href="<?php echo $_ENV['URL_ADM']; ?>training-matrix-manager" class="btn btn-warning btn-sm me-1 mb-1">
                    <i class="fas fa-table"></i> Matriz
                </a>
                <a href="<?php echo $_ENV['URL_ADM']; ?>list-training-status" class="btn btn-info btn-sm me-1 mb-1">
                    <i class="fas fa-chart-bar"></i> Status
                </a>
            </span>
        </div>
        <div class="card-body">
            <?php include './app/adms/Views/partials/alerts.php'; ?>
            <form method="GET" class="row g-2 mb-3 align-items-end">
                <div class="col-md-1">
                    <input type="text" name="codigo" class="form-control" placeholder="Digite parte do código" value="<?= htmlspecialchars($_GET['codigo'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="text" name="nome" class="form-control" placeholder="Digite parte do nome do treinamento" value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="text" name="instrutor" class="form-control" placeholder="Digite parte do nome do instrutor" value="<?= htmlspecialchars($_GET['instrutor'] ?? '') ?>">
                </div>
                <div class="col-md-1">
                    <select name="reciclagem" class="form-select">
                        <option value="">Reciclagem</option>
                        <option value="1" <?= (($_GET['reciclagem'] ?? '') === '1') ? 'selected' : '' ?>>Sim</option>
                        <option value="0" <?= (($_GET['reciclagem'] ?? '') === '0') ? 'selected' : '' ?>>Não</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="tipo" class="form-select">
                        <option value="">Tipo</option>
                        <option value="Presencial" <?= (($_GET['tipo'] ?? '') === 'Presencial') ? 'selected' : '' ?>>Presencial</option>
                        <option value="Online" <?= (($_GET['tipo'] ?? '') === 'Online') ? 'selected' : '' ?>>Online</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="ativo" class="form-select">
                        <option value="">Status</option>
                        <option value="1" <?= (($_GET['ativo'] ?? '') === '1') ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= (($_GET['ativo'] ?? '') === '0') ? 'selected' : '' ?>>Inativo</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <label for="per_page" class="form-label mb-1 me-2">Mostrar</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm w-auto mx-1" onchange="this.form.submit()">
                        <?php foreach ([10, 20, 50, 100] as $opt): ?>
                            <option value="<?= $opt ?>" <?= ($_GET['per_page'] ?? 10) == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="form-label mb-1 ms-1">registros</span>
                    <button type="submit" class="btn btn-primary btn-sm ms-3"><i class="fa fa-search"></i> Filtrar</button>
                    <a href="<?php echo $_ENV['URL_ADM']; ?>list-trainings" class="btn btn-secondary btn-sm ms-2"><i class="fa fa-times"></i> Limpar Filtros</a>
                </div>
            </form>
            <!-- <?php if (!empty($this->data['pagination'])): ?>
                <nav>
                    <?= $this->data['pagination']; ?>
                </nav>
            <?php endif; ?> -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="table-layout: fixed; width: 100%;">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:60px;" class="text-center">ID</th>
                            <th style="width:100px;">Código</th>
                            <th class="col-nome">Nome</th>
                            <th style="width:80px;">Versão</th>
                            <th style="width:100px;">Reciclar</th>
                            <th style="width:100px;">Tipo</th>
                            <th style="width:120px;">Instrutor</th>
                            <th style="width:100px;" class="text-center">Carga Horária</th>
                            <th style="width:120px;" class="text-center">Cargos Vinculados</th>
                            <th style="width:80px;" class="text-center">Status</th>
                            <th style="width:200px;" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($this->data['trainings'])): ?>
                            <?php foreach ($this->data['trainings'] as $training): ?>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($training['id']); ?></span>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($training['codigo']); ?></strong>
                                    </td>
                                    <td class="col-nome">
                                        <div>
                                            <strong><?php echo htmlspecialchars($training['nome']); ?></strong>
                                            <?php if (!empty($training['versao'])): ?>
                                                <br><small class="text-muted">v<?php echo htmlspecialchars($training['versao']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($training['versao'])): ?>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($training['versao']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($training['reciclagem']) && !empty($training['reciclagem_periodo'])): ?>
                                            <?php echo FormatHelper::formatReciclagemPeriodoTable((int)$training['reciclagem_periodo']); ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($training['tipo'])): ?>
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($training['tipo']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($training['instructor_name'])) {
                                            echo '<i class="fas fa-user-tie me-1"></i>' . htmlspecialchars($training['instructor_name']);
                                        } elseif (!empty($training['user_name'])) {
                                            echo '<i class="fas fa-user-tie me-1"></i>' . htmlspecialchars($training['user_name']);
                                        } elseif (!empty($training['instrutor'])) {
                                            echo htmlspecialchars($training['instrutor']);
                                        } else {
                                            echo '<span class="text-muted">-</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($training['carga_horaria'])): ?>
                                            <span class="badge bg-warning text-dark">
                                                <?php echo htmlspecialchars($training['carga_horaria']); ?>h
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $cargosVinculados = (int)($training['cargos_vinculados'] ?? 0);
                                        $colaboradoresVinculados = (int)($training['colaboradores_vinculados'] ?? 0);
                                        if ($cargosVinculados > 0): 
                                        ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-link me-1"></i><?php echo $cargosVinculados; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-unlink me-1"></i>0
                                            </span>
                                        <?php endif; ?>
                                        <span class="badge bg-primary ms-1">
                                            <i class="fas fa-users me-1"></i><?php echo $colaboradoresVinculados; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($training['ativo']): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Ativo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Inativo
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo $_ENV['URL_ADM']; ?>view-training/<?php echo $training['id']; ?>" 
                                               class="btn btn-primary btn-sm" 
                                               title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo $_ENV['URL_ADM']; ?>update-training/<?php echo $training['id']; ?>" 
                                               class="btn btn-warning btn-sm" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?php echo $_ENV['URL_ADM']; ?>training-positions/<?php echo $training['id']; ?>" 
                                               class="btn btn-info btn-sm" 
                                               title="Vincular Cargos">
                                                <i class="fas fa-link"></i>
                                            </a>
                                            <a href="<?php echo $_ENV['URL_ADM']; ?>link-training-users/<?php echo $training['id']; ?>"
                                               class="btn btn-secondary btn-sm"
                                               title="Vincular Colaborador(es)">
                                                <i class="fas fa-user-plus"></i>
                                            </a>
                                            <a href="<?php echo $_ENV['URL_ADM']; ?>delete-training/<?php echo $training['id']; ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Tem certeza que deseja excluir este treinamento?');"
                                               title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Nenhum treinamento encontrado.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($this->data['pagination']['html'])): ?>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="text-secondary small">
                        <?php if (!empty($this->data['pagination']['total'])): ?>
                            Mostrando <?= $this->data['pagination']['first_item'] ?> até <?= $this->data['pagination']['last_item'] ?> de <?= $this->data['pagination']['total'] ?> registro(s)
                        <?php else: ?>
                            Exibindo <?= count($this->data['trainings'] ?? []) ?> registro(s) nesta página.
                        <?php endif; ?>
                    </div>
                    <div>
                        <?= $this->data['pagination']['html'] ?? '' ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="mt-2 text-end text-muted small d-none">
                Exibindo <?= count($this->data['trainings'] ?? []) ?> registro(s) nesta página.
            </div>
        </div>
    </div>
</div> 
<?php
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
            <span>Listar</span>
            <span class="ms-auto d-sm-flex flex-row">
                <a href="<?php echo $_ENV['URL_ADM']; ?>create-training" class="btn btn-success btn-sm me-1 mb-1">
                    <i class="fa-solid fa-plus"></i> Cadastrar
                </a>
            </span>
        </div>
        <div class="card-body">
            <?php include './app/adms/Views/partials/alerts.php'; ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Versão</th>
                            <th>Validade</th>
                            <th>Tipo</th>
                            <th>Instrutor</th>
                            <th>Carga Horária</th>
                            <th>Cargos Vinculados</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($this->data['trainings'])): ?>
                            <?php foreach ($this->data['trainings'] as $training): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($training['id']); ?></td>
                                    <td><?php echo htmlspecialchars($training['codigo']); ?></td>
                                    <td><?php echo htmlspecialchars($training['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($training['versao']); ?></td>
                                    <td><?php echo htmlspecialchars($training['validade']); ?></td>
                                    <td><?php echo htmlspecialchars($training['tipo']); ?></td>
                                    <td><?php echo htmlspecialchars($training['instrutor']); ?></td>
                                    <td><?php echo htmlspecialchars($training['carga_horaria']); ?></td>
                                    <td><?php echo (int)($training['cargos_vinculados'] ?? 0); ?></td>
                                    <td>
                                        <?php echo $training['ativo'] ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-danger">Inativo</span>'; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $_ENV['URL_ADM']; ?>update-training/<?php echo $training['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                        <a href="<?php echo $_ENV['URL_ADM']; ?>delete-training/<?php echo $training['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este treinamento?');">Excluir</a>
                                        <a href="<?php echo $_ENV['URL_ADM']; ?>training-positions/<?php echo $training['id']; ?>" class="btn btn-info btn-sm">Vincular Cargos</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="10" class="text-center">Nenhum treinamento encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 
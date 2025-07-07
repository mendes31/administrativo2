<?php

use App\adms\Helpers\CSRFHelper;

// Gera o token CSRF para proteger o formulário de deleção
$csrf_token = CSRFHelper::generateCSRFToken('form_delete_user');

?>

<div class="container-fluid px-4">

    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Usuários</h2>

        <ol class="breadcrumb  mb-3 ms-auto">
            <li class="breadcrumb-item"><a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item">Usuários</li>
        </ol>

    </div>
    <div class="card mb-4 border-light shadow">
        <div class="card-header hstack gap-2">
            <span>
                Listar
            </span>

            <span class="ms-auto">
                <?php
                if (in_array('CreateUser', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}create-user' class='btn btn-success btn-sm'><i class='fa-regular fa-square-plus'></i> Cadastrar</a> ";
                }
                ?>
            </span>
        </div>

        <div class="card-body">

            <?php
            // Inclui o arquivo que exibe mensagens de sucesso e erro
            include './app/adms/Views/partials/alerts.php';
            ?>
            <form method="get" class="row g-2 mb-3 align-items-end">
                <div class="col-md-3">
                    <label for="nome" class="form-label mb-1">Nome</label>
                    <input type="text" name="nome" id="nome" class="form-control" value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="email" class="form-label mb-1">E-mail</label>
                    <input type="text" name="email" id="email" class="form-control" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <label for="per_page" class="form-label mb-1 me-2">Mostrar</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm w-auto mx-1" onchange="this.form.submit()">
                        <?php foreach ([10, 20, 50, 100] as $opt): ?>
                            <option value="<?= $opt ?>" <?= ($this->data['per_page'] ?? 10) == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="form-label mb-1 ms-1">registros</span>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="?limpar_filtros=1" class="btn btn-secondary ms-2">Limpar Filtros</a>
                </div>
            </form>
            <?php
            // Verifica se há usuários no array
            if ($this->data['users'] ?? false) {
            ?>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nome</th>
                            <th scope="col" class="d-none d-md-table-cell">E-mail</th>
                            <th scope="col" class="d-none d-md-table-cell">Usuário</th>
                            <th scope="col" class="d-none d-md-table-cell">Departamento</th>
                            <th scope="col" class="d-none d-md-table-cell">Cargo</th>
                            <th scope="col" class="d-none d-md-table-cell">Status</th>
                            <th scope="col" class="d-none d-md-table-cell">Bloqueado</th>
                            <th scope="col" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->data['users'] as $user) { extract($user); ?>
                            <tr>
                                <th><?= $id; ?></th>
                                <td><?= $name; ?></td>
                                <td class="d-none d-md-table-cell"><?= $email; ?></td>
                                <td class="d-none d-md-table-cell"><?= $username ?></td>
                                <td class="d-none d-md-table-cell"><?= $name_dep ?></td>
                                <td class="d-none d-md-table-cell"><?= $name_pos ?></td>
                                <td class="d-none d-md-table-cell"><?= $status ?></td>
                                <td class="d-none d-md-table-cell"><?= $bloqueado ?></td>
                                <td class="text-center">
                                    <?php
                                    if (in_array('ViewUser', $this->data['buttonPermission'])) {
                                        echo "<a href='{$_ENV['URL_ADM']}view-user/$id' class='btn btn-info btn-sm me-1 mb-1'><i class='fa-regular fa-eye'></i> Visualizar</a> ";
                                    }
                                    if (in_array('UpdateUser', $this->data['buttonPermission'])) {
                                        echo "<a href='{$_ENV['URL_ADM']}update-user/$id' class='btn btn-warning btn-sm me-1 mb-1'><i class='fa-regular fa-pen-to-square'></i> Editar</a> ";
                                    }
                                    if (in_array('DeleteUser', $this->data['buttonPermission'])) {
                                    ?>
                                        <form id="formDelete<?= $id; ?>" action="<?= $_ENV['URL_ADM']; ?>delete-user" method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                                            <input type="hidden" name="id" id="id" value="<?= $id ?? ''; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm me-1 mb-1" onclick="confirmDeletion(event, <?= $id; ?>)"><i class="fa-regular fa-trash-can"></i> Apagar</button>
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="text-secondary small">
                        <?php if (!empty($this->data['pagination']['total'])): ?>
                            Mostrando <?= $this->data['pagination']['first_item'] ?> até <?= $this->data['pagination']['last_item'] ?> de <?= $this->data['pagination']['total'] ?> registro(s)
                        <?php else: ?>
                            Exibindo <?= count($this->data['users']); ?> registro(s) nesta página.
                        <?php endif; ?>
                    </div>
                    <div>
                        <?= $this->data['pagination']['html'] ?? '' ?>
                    </div>
                </div>
            <?php } else {
                echo "<div class='alert alert-danger' role='alert'>Nenhum usuário encontrado.</div>";
            } ?>

        </div>

    </div>
</div>
<?php

use App\adms\Helpers\CSRFHelper;

// Gera o token CSRF para proteger o formulário de deleção
$csrf_token = CSRFHelper::generateCSRFToken('form_delete_bank');

?>

<div class="container-fluid px-4">

    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Bancos</h2>

        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">Bancos</li>

        </ol>

    </div>

    <div class="card mb-4 border-light shadow">

        <div class="card-header hstack gap-2">
            <span>Listar</span>

            <span class="ms-auto">
            <?php
                if (in_array('CreateBank', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}create-bank' class='btn btn-success btn-sm'><i class='fa-regular fa-square-plus'></i> Cadastrar</a> ";
                }
                ?>
            </span>
        </div>

        <div class="card-body">

            <?php // Inclui o arquivo que exibe mensagens de sucesso e erro
            include './app/adms/Views/partials/alerts.php';

            // Verifica se há banco no array
            if ($this->data['banks'] ?? false) {
            ?>

                <form method="get" class="row g-2 mb-3 align-items-end">
                    <div class="col-md-2">
                        <label for="bank_name" class="form-label mb-1">Nome do Banco</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control" value="<?= htmlspecialchars($this->data['filtros']['bank_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="bank_code" class="form-label mb-1">Código</label>
                        <input type="text" name="bank_code" id="bank_code" class="form-control" value="<?= htmlspecialchars($this->data['filtros']['bank_code'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="agency" class="form-label mb-1">Agência</label>
                        <input type="text" name="agency" id="agency" class="form-control" value="<?= htmlspecialchars($this->data['filtros']['agency'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="account" class="form-label mb-1">Conta</label>
                        <input type="text" name="account" id="account" class="form-control" value="<?= htmlspecialchars($this->data['filtros']['account'] ?? '') ?>">
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
                    <div class="col-md-2 mt-2">
                        <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
                        <a href="?limpar_filtros=1" class="btn btn-secondary mt-4 ms-2">Limpar Filtros</a>
                    </div>
                </form>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Banco</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Conta</th>
                            <th scope="col">Agência</th>
                            <th scope="col">Saldo</th>
                            <th scope="col" class="text-center">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        // Percorre o array de cargo
                        foreach ($this->data['banks'] as $bank) {

                            // Extrai variáveis do array de cargos
                            extract($bank); ?>
                            <tr>
                                <td><?php echo $id; ?></td>
                                <td><?php echo $bank_name; ?></td>
                                <td><?php echo $bank; ?></td>
                                <td><?php echo $type; ?></td>
                                <td><?php echo $account; ?></td>
                                <td><?php echo $agency; ?></td>
                                <td><?php echo $balance; ?></td>
                                <td class="text-center">

                                    <?php

                                    if (in_array('ViewBank', $this->data['buttonPermission'])) {
                                        echo "<a href='{$_ENV['URL_ADM']}view-bank/$id' class='btn btn-primary btn-sm me-1 mb-1'><i class='fa-regular fa-eye'></i> Visualizar</a>";
                                    }

                                    if (in_array('UpdateBank', $this->data['buttonPermission'])) {
                                        echo "<a href='{$_ENV['URL_ADM']}update-bank/$id' class='btn btn-warning btn-sm me-1 mb-1'><i class='fa-solid fa-pen-to-square'></i> Editar</a>";
                                    }

                                    if (in_array('DeleteBank', $this->data['buttonPermission'])) {
                                    ?>

                                        <form id="formDelete<?php echo $id; ?>" action="<?php echo $_ENV['URL_ADM']; ?>delete-bank" method="POST" class="d-inline">

                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                            <input type="hidden" name="id" id="id" value="<?php echo $id ?? ''; ?>">

                                            <input type="hidden" name="bank_name" id="bank_name" value="<?php echo $bank_name ?? ''; ?>">

                                            <button type="submit" class="btn btn-danger btn-sm me-1 mb-1" onclick="confirmDeletion(event, <?php echo $id; ?>)"><i class="fa-regular fa-trash-can"></i> Apagar</button>

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
                            Exibindo <?= count($this->data['banks']); ?> registro(s) nesta página.
                        <?php endif; ?>
                    </div>
                    <div>
                        <?= $this->data['pagination']['html'] ?? '' ?>
                    </div>
                </div>

            <?php
            } else { // Exibe mensagem se nenhum banco for encontrado
                echo "<div class='alert alert-danger' role='alert'>Nenhum Banco encontrado!</div>";
            } ?>

        </div>

    </div>
</div>

<script type="text/javascript">
    // DataTables removido para padronização do sistema
</script>
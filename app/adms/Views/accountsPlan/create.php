<?php

use App\adms\Helpers\CSRFHelper;

?>
<div class="container-fluid px-4">

    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Plano de Contas</h2>

        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>list-accounts-plan" class="text-decoration-none">Plano de Contas</a>
            </li>
            <li class="breadcrumb-item">Cadastrar</li>

        </ol>

    </div>

    <div class="card mb-4 border-light shadow">

        <div class="card-header hstack gap-2">
            <span>Cadastrar</span>

            <span class="ms-auto d-sm-flex flex-row">

            <?php
                if (in_array('ListAccountsPlan', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}list-accounts-plan' class='btn btn-info btn-sm me-1 mb-1'><i class='fa-solid fa-list'></i>Listar</a> ";
                }
                ?>
            </span>

        </div>

        <div class="card-body">

            <?php include './app/adms/Views/partials/alerts.php'; ?>

            <!-- Formulário para cadastrar um novo Palno de contas -->
            <form action="" method="POST" class="row g-3">

                <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken('form_create_account_plan'); ?>">

                <div class="col-6">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Nome do Plano de Contas" value="<?php echo $this->data['form']['name'] ?? ''; ?>">
                </div>

                <div class="col-6">
                    <label for="account" class="form-label">Dias</label>
                    <input type="text" name="account" class="form-control" id="account" placeholder="Digite o nº da conta." value="<?php echo $this->data['form']['account'] ?? ''; ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm">Cadastrar</button>
                </div>

            </form>

        </div>

    </div>

</div>
<?php

use App\adms\Helpers\CSRFHelper;

?>

<div class="container-fluid px-4">

    <div class="mb-1 d-flex flex-column flex-sm-row gap-2">
        <h2 class="mt-3">Departamentos</h2>

        <ol class="breadcrumb mb-3 mt-0 mt-sm-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>list-departments" class="text-decoration-none">Departamentos</a>
            </li>
            <li class="breadcrumb-item">Editar</li>

        </ol>

    </div>

    <div class="card mb-4 border-light shadow">

        <div class="card-header hstack gap-2">

            <span>Editar</span>

            <span class="ms-auto d-sm-flex flex-row">
            <?php
                if (in_array('ListDepartments', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}list-departments' class='btn btn-info btn-sm me-1 mb-1'><i class='fa-solid fa-list'></i> Listar</a> ";
                }

                $id = ($this->data['form']['id'] ?? '');
                if (in_array('ViewDepartment', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}view-department/$id' class='btn btn-primary btn-sm me-1 mb-1'><i class='fa-regular fa-eye'></i> Visualizar</a> ";
                }
                ?>
            </span>

        </div>

        <div class="card-body">

            <?php include './app/adms/Views/partials/alerts.php'; ?>

            <form action="" method="POST" class="row g-3">

                <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken('form_update_departments'); ?>">

                <input type="hidden" name="id" id="id" value="<?php echo $this->data['form']['id'] ?? ''; ?>">

                <div class="col-12">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Nome do departamento" value="<?php echo $this->data['form']['name'] ?? ''; ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-warning btn-sm">Salvar</button>
                </div>

            </form>

        </div>
    </div>

</div>
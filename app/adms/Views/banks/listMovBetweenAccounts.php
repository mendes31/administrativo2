<?php

use App\adms\Helpers\CSRFHelper;

// Token CSRF para exclusão futura (caso adicione botão de deletar)
$csrf_token = CSRFHelper::generateCSRFToken('form_delete_mov_between_accounts');

?>

<div class="container-fluid px-4">

    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Movimentações Entre Contas</h2>

        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">Movimentações</li>
        </ol>
    </div>

    <div class="card mb-4 border-light shadow">

        <div class="card-header hstack gap-2">
            <span>Listar</span>

            <span class="ms-auto">
                <?php
                if (in_array('MovBetweenAccounts', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}mov-between-accounts' class='btn btn-success btn-sm'><i class='fa-regular fa-square-plus'></i> Nova Movimentação</a> ";
                }
                ?>
            </span>
        </div>

        <div class="card-body">

            <?php include './app/adms/Views/partials/alerts.php'; ?>

            <?php if (!empty($this->data['movBetweenAccounts'])) { ?>

                <table class="table table-striped table-hover" id="tabela">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Conta Origem</th>
                            <th>Conta Destino</th>
                            <th>Valor</th>
                            <th>Descrição</th>
                            <th>Usuário</th>
                            <th>Data</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($this->data['movBetweenAccounts'] as $mov) {
                            extract($mov); ?>
                            <tr>
                                <td><?php echo $id; ?></td>
                                <td><?php echo $from_bank_name; ?></td>
                                <td><?php echo $to_bank_name; ?></td>
                                <td>R$ <?php echo number_format($amount, 2, ',', '.'); ?></td>
                                <td><?php echo $description; ?></td>
                                <td><?php echo $user_name; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($created_at)); ?></td>
                                <td class="text-center">

                                    <?php
                                    if (in_array('ViewMovBetweenAccounts', $this->data['buttonPermission'])) {
                                        echo "<a href='{$_ENV['URL_ADM']}view-transfer/$id' class='btn btn-primary btn-sm me-1 mb-1'><i class='fa-regular fa-eye'></i> Visualizar</a>";
                                    }

                                    // Se no futuro você quiser permitir edição/deleção:
                                    // if (in_array('UpdateMovBetweenAccounts', $this->data['buttonPermission'])) { ... }
                                    ?>

                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>

                <?php include_once './app/adms/Views/partials/pagination.php'; ?>

            <?php } else {
                echo "<div class='alert alert-danger' role='alert'>Nenhuma movimentação encontrada!</div>";
            } ?>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#tabela').DataTable({
            "language": {
                "decimal": ",",
                "thousands": ".",
                "sProcessing": "Processando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "Nenhum registro encontrado",
                "sEmptyTable": "Nenhum dado disponível na tabela",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primeiro",
                    "sPrevious": "Anterior",
                    "sNext": "Próximo",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            }
        });
    });
</script>

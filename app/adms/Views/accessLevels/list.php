<?php

use App\adms\Helpers\CSRFHelper;

// Gera o token CSRF para proteger o formulário de deleção
$csrf_token = CSRFHelper::generateCSRFToken('form_delete_access_level');

?>

<div class="container-fluid px-4">

    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Níveis de Acesso</h2>

        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">Níveis de Acesso</li>

        </ol>

    </div>

    <div class="card mb-4 border-light shadow">

        <div class="card-header hstack gap-2">
            <span>Listar</span>

            <span class="ms-auto">

                <?php
                if (in_array('CreateAccessLevel', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}create-access-level' class='btn btn-success btn-sm'><i class='fa-regular fa-square-plus'></i> Cadastrar</a> ";
                }

                if (in_array('AccessLevelPageSync', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}access-level-page-sync' class='btn btn-warning btn-sm' onclick='showLoading()'><i class='fa-solid fa-rotate'></i> Sincronizar</a> ";
                }
                ?>
            </span>
        </div>

        <div class="card-body">

            <?php // Inclui o arquivo que exibe mensagens de sucesso e erro
            include './app/adms/Views/partials/alerts.php';


            // Verifica se há níveis de acesso no array
            if ($this->data['accessLevels'] ?? false) {
            ?>

                <table class="table table-striped table-hover" id="tabela">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nome</th>
                            <th scope="col" class="text-center">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        // Percorre o array de níveis de acesso
                        foreach ($this->data['accessLevels'] as $accessLevel) {

                            // Extrai variáveis do array de níveis de acesso
                            extract($accessLevel); ?>
                            <tr>
                                <td><?php echo $id; ?></td>
                                <td><?php echo $name; ?></td>
                                <td class="text-center">

                                    <?php
                                    if (in_array('ListAccessLevelsPermissions', $this->data['buttonPermission'])) {
                                        echo "<a href='{$_ENV['URL_ADM']}list-access-levels-permissions/$id' class='btn btn-info btn-sm me-1 mb-1'><i class='fa-solid fa-lock-open'></i> Permissões</a>";
                                    }

                                    if (in_array('ViewAccessLevel', $this->data['buttonPermission'])) {
                                        echo "<a href='{$_ENV['URL_ADM']}view-access-level/$id' class='btn btn-primary btn-sm me-1 mb-1'><i class='fa-regular fa-eye'></i> Visualizar</a>";
                                    }

                                    if (in_array('UpdateAccessLevel', $this->data['buttonPermission'])) {
                                        echo "<a href='{$_ENV['URL_ADM']}update-access-level/$id' class='btn btn-warning btn-sm me-1 mb-1'><i class='fa-solid fa-pen-to-square'></i> Editar</a>";
                                    }

                                    if (in_array('DeleteAccessLevel', $this->data['buttonPermission'])) {
                                    ?>

                                        <form id="formDelete<?php echo $id; ?>" action="<?php echo $_ENV['URL_ADM']; ?>delete-access-level" method="POST" class="d-inline">

                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                            <input type="hidden" name="id" id="id" value="<?php echo $id ?? ''; ?>">

                                            <input type="hidden" name="name" id="name" value="<?php echo $name ?? ''; ?>">

                                            <button type="submit" class="btn btn-danger btn-sm me-1 mb-1" onclick="confirmDeletion(event, <?php echo $id; ?>)"><i class="fa-regular fa-trash-can"></i> Apagar</button>

                                        </form>
                                    <?php } ?>

                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>
                </table>

            <?php
                // Inclui o arquivo de paginação
                include_once './app/adms/Views/partials/pagination.php';
            } else { // Exibe mensagem se nenhum nível de acesso for encontrado
                echo "<div class='alert alert-danger' role='alert'>Nenhum nível de acesso encontrado!</div>";
            } ?>

        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
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
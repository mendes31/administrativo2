<?php

use App\adms\Helpers\CSRFHelper;

// Gera o token CSRF para proteger o formulário de deleção
$csrf_token = CSRFHelper::generateCSRFToken('form_delete_document');

?>

<div class="container-fluid px-4">

    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Documentos</h2>

        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">Documentos</li>

        </ol>

    </div>

    <div class="card mb-4 border-light shadow">

        <div class="card-header hstack gap-2">
            <span>Listar</span>

            <span class="ms-auto">
                <?php
                if (in_array('CreateDocument', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}create-document' class='btn btn-success btn-sm'><i class='fa-regular fa-square-plus'></i> Cadastrar</a> ";
                }
                ?>
            </span>
        </div>

        <div class="card-body">

            <?php // Inclui o arquivo que exibe mensagens de sucesso e erro
            include './app/adms/Views/partials/alerts.php';

            // Verifica se há documento no array
            if ($this->data['documents'] ?? false) {
            ?>

                <table class="table table-striped table-hover" id="tabela">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Código</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Versão</th>
                            <th scope="col" class="d-none d-md-table-cell">Status</th>
                            <th scope="col" class="text-center">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        // Percorre o array de documento
                        foreach ($this->data['documents'] as $document) {

                            // Extrai variáveis do array de documento
                            extract($document); ?>
                            <tr>
                                <td><?php echo $id; ?></td>
                                <td><?php echo $cod_doc; ?></td>
                                <td><?php echo $name_doc; ?></td>
                                <td><?php echo $version; ?></td>
                                <td class="d-none d-md-table-cell">
                                    <?php echo $active ? "<span class='badge text-bg-success'>Ativa</span>" : "<span class='badge text-bg-danger'>Inativa</span>"; ?>
                                </td>


                                <td class="text-center">
                                    <?php
                                     if (in_array('ListDocumentPositions', $this->data['buttonPermission'])) {
                                        echo "<a href='{$_ENV['URL_ADM']}list-document-positions/$id' class='btn btn-info btn-sm me-1 mb-1'><i class='fa-solid fa-lock-open'></i> Cargos/Treinamentos</a>";
                                    }
                                        if (in_array('ViewDocument', $this->data['buttonPermission'])) {
                                        echo "<a href='{$_ENV['URL_ADM']}view-document/$id' class='btn btn-primary btn-sm me-1 mb-1'><i class='fa-regular fa-eye'></i> Visualizar</a>";
                                    }

                                    if (in_array('UpdateDocument', $this->data['buttonPermission'])) {
                                        echo "<a href='{$_ENV['URL_ADM']}update-document/$id' class='btn btn-warning btn-sm me-1 mb-1'><i class='fa-solid fa-pen-to-square'></i> Editar</a>";
                                    }

                                    if (in_array('DeleteDocument', $this->data['buttonPermission'])) {
                                    ?>
                                        <form id="formDelete<?php echo $id; ?>" action="<?php echo $_ENV['URL_ADM']; ?>delete-document" method="POST" class="d-inline">

                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                            <input type="hidden" name="id" id="id" value="<?php echo $id ?? ''; ?>">

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
            } else { // Exibe mensagem se nenhum página for encontrado
                echo "<div class='alert alert-danger' role='alert'>Documento não encontrado!</div>";
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
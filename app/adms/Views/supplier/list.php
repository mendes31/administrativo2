<?php

use App\adms\Helpers\CSRFHelper;

// Gera o token CSRF para proteger o formulário de deleção
$csrf_token = CSRFHelper::generateCSRFToken('form_delete_supplier');

?>

<div class="container-fluid px-4">

    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Forncedores</h2>

        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">Forncedores</li>

        </ol>

    </div>

    <div class="card mb-4 border-light shadow">

        <div class="card-header">
            <div class="row align-items-center g-3 flex-column flex-md-row">
                <!-- Texto "Listar" -->
                <div class="col-md-auto">
                    <span class="fw-bold">Listar</span>
                </div>

                <!-- Formulário de busca -->
                <div class="col-md-auto flex-fill">
                    <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
                        <input type="text" name="search" value="<?= $criteria['search'] ?? '' ?>" placeholder="Buscar por nome, código ou status...">
                        <label for="per_page" class="form-label mb-1 ms-2">Exibir</label>
                        <select name="per_page" id="per_page" class="form-select form-select-sm w-auto ms-1" onchange="this.form.submit()">
                            <?php foreach ([10, 20, 50, 100] as $opt): ?>
                                <option value="<?= $opt ?>" <?= ($_GET['per_page'] ?? 20) == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="ms-2">por página</span>
                        <button type="submit" class="btn btn-primary btn-sm ms-2"><i class="fa fa-search"></i> Buscar</button>
                        <a href="?limpar_filtros=1" class="btn btn-secondary btn-sm ms-2">Limpar Filtros</a>
                    </form>
                </div>

                <!-- Upload e botão Cadastrar -->
                <div class="col-md-auto d-flex flex-wrap gap-2">
                    <form method="POST" action="<?php echo $_ENV['URL_ADM'] . 'process-file'; ?>" enctype="multipart/form-data" class="d-flex flex-wrap align-items-center gap-2">
                        <label class="mb-0">Arquivo:</label>
                        <input type="file" name="arquivo" id="arquivo" class="form-control form-control-sm">
                        <input type="submit" value="Enviar" class="btn btn-secondary btn-sm">
                    </form>

                    <?php
                    if (in_array('CreateSupplier', $this->data['buttonPermission'])) {
                        echo "<a href='{$_ENV['URL_ADM']}create-supplier' class='btn btn-success btn-sm'><i class='fa-regular fa-square-plus'></i> Cadastrar</a>";
                    }
                    ?>
                </div>
            </div>
        </div>




        <div class="card-body">

            <?php // Inclui o arquivo que exibe mensagens de sucesso e erro
            include './app/adms/Views/partials/alerts.php';

            // Verifica se há Fornecedor no array
            if ($this->data['suppliers'] ?? false) {
            ?>

                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nome</th>
                            <th scope="col" class="d-none d-md-table-cell">Pessoa</th>
                            <th scope="col" class="d-none d-md-table-cell">Documetento</th>
                            <th scope="col" class="d-none d-md-table-cell">Telefone</th>
                            <th scope="col" class="d-none d-md-table-cell">Email</th>
                            <th scope="col" class="d-none d-md-table-cell">Endereço</th>
                            <th scope="col" class="d-none d-md-table-cell">Descrição/Observações</th>
                            <th scope="col" class="d-none d-md-table-cell">Data Nascimetno</th>
                            <th scope="col">Ativo</th>
                            <th scope="col" class="text-center">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        // Percorre o array de Forncedores
                        foreach ($this->data['suppliers'] as $supplier) {

                            // Extrai variáveis do array de Forncedores
                            extract($supplier); ?>
                            <tr>
                                <td><?php echo $card_code; ?></td>
                                <td><?php echo $card_name; ?></td>
                                <td class="d-none d-md-table-cell"><?php echo $type_person; ?></td>
                                <td class="d-none d-md-table-cell"><?php echo $doc; ?></td>
                                <td class="d-none d-md-table-cell"><?php echo $phone; ?></td>
                                <td class="d-none d-md-table-cell"><?php echo $email; ?></td>
                                <td class="d-none d-md-table-cell"><?php echo $address; ?></td>
                                <td class="d-none d-md-table-cell"><?php echo $description; ?></td>
                                <td class="d-none d-md-table-cell"><?php echo $date_birth; ?></td>
                                <td><?php echo ($active == 1) ? 'SIM' : 'NÃO'; ?></td>

                                <td class="text-center">
                                    <div class="tabela-acoes">
                                        <!-- <div class="d-flex flex-wrap gap-1 flex-md-row flex-column justify-content-center"> -->

                                        <?php
                                        // if (in_array('ViewSupplier', $this->data['buttonPermission'])) {
                                        //     echo "<a href='{$_ENV['URL_ADM']}view-supplier/$id' class='btn btn-primary btn-sm me-1 mb-1'><i class='fa-regular fa-eye'></i> Visualizar</a>";

                                        // }

                                        if (in_array('ViewSupplier', $this->data['buttonPermission'])) {
                                            echo "<a href='{$_ENV['URL_ADM']}view-supplier/$id'
                                            class='btn btn-primary btn-sm me-1 mb-1' 
                                            data-bs-toggle='tooltip' 
                                            data-bs-placement='top' 
                                            data-bs-custom-class='tooltip-visualizar' 
                                            title='Visualizar'>
                                            <i class='fa-regular fa-eye'></i>
                                          </a>";
                                        }

                                        // if (in_array('UpdateSupplier', $this->data['buttonPermission'])) {
                                        //     echo "<a href='{$_ENV['URL_ADM']}update-supplier/$id' class='btn btn-warning btn-sm me-1 mb-1'><i class='fa-solid fa-pen-to-square'></i> Editar</a>";
                                        // }

                                        if (in_array('UpdateSupplier', $this->data['buttonPermission'])) {
                                            echo "<a href='{$_ENV['URL_ADM']}update-supplier/$id'
                                            class='btn btn-warning btn-sm me-1 mb-1' 
                                            data-bs-toggle='tooltip' 
                                            data-bs-placement='top' 
                                            data-bs-custom-class='tooltip-editar' 
                                            title='Editar'>
                                            <i class='fa-solid fa-pen-to-square'></i>
                                          </a>";
                                        }

                                        if (in_array('DeleteSupplier', $this->data['buttonPermission'])) {
                                        ?>

                                            <form id="formDelete<?php echo $id; ?>" action="<?php echo $_ENV['URL_ADM']; ?>delete-supplier" method="POST" class="d-inline">

                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                                <input type="hidden" name="id" id="id" value="<?php echo $id ?? ''; ?>">

                                                <input type="hidden" name="card_name" id="card_name" value="<?php echo $card_name ?? ''; ?>">

                                                <!-- <button type="submit" class="btn btn-danger btn-sm me-1 mb-1" onclick="confirmDeletion(event, <?php echo $id; ?>)"><i class="fa-regular fa-trash-can"></i> Apagar</button> -->

                                                <button type="submit"
                                                    class="btn btn-danger btn-sm me-1 mb-1"
                                                    onclick="confirmDeletion(event, <?php echo $id; ?>)"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    data-bs-custom-class="tooltip-deletar"
                                                    title="Excluir">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>

                                            </form>
                                        <?php } ?>
                                    </div>

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
                            Exibindo <?= count($this->data['suppliers']); ?> registro(s) nesta página.
                        <?php endif; ?>
                    </div>
                    <div>
                        <?= $this->data['pagination']['html'] ?? '' ?>
                    </div>
                </div>


            <?php
            } else { // Exibe mensagem se nenhum fornecedor for encontrado
                echo "<div class='alert alert-danger' role='alert'>Nenhum Fornecedor encontrado!</div>";
            } ?>

        </div>

    </div>
</div>

<!-- <script type="text/javascript">
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
</script> -->

<script type="text/javascript">
    $(document).ready(function() {
        const table = $('#tabela').DataTable({
            "scrollX": true, // Ativa rolagem horizontal
            "autoWidth": false, // Impede que as colunas fiquem largas demais
            "responsive": true, // Torna a tabela responsiva
            "paging": true, // Mantém a paginação ativada
            "lengthChange": false, // Oculta opção de alterar quantidade de registros
            "info": false, // Remove a informação "Mostrando X de Y"
            "columnDefs": [{
                    "width": "100px",
                    "targets": "_all"
                } // Reduz a largura mínima das colunas
            ],
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
            },
            columnDefs: [{
                className: "text-start",
                targets: "_all"
            }]

        });

    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
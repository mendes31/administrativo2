<?php

use App\adms\Helpers\CSRFHelper;

// Gera o token CSRF para proteger o formulário de deleção
$csrf_token = CSRFHelper::generateCSRFToken('form_update_access_level_permissions');

?>

<div class="container-fluid px-4">

    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Permissões</h2>

        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>list-access-levels" class="text-decoration-none">Níveis de Acesso</a>
            </li>
            <li class="breadcrumb-item">Permissões</li>

        </ol>

    </div>

    <div class="card mb-4 border-light shadow">

        <div class="card-header hstack gap-2">
            <span><?php echo $this->data['accessLevel']['name'] ?? 'Listar'; ?></span>
            
            <span class="ms-auto d-sm-flex flex-row">
                <?php
                if (in_array('ListAccessLevels', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}list-access-levels' class='btn btn-info btn-sm me-1 mb-1'><i class='fa-solid fa-list'></i> Listar</a> ";
                }
                ?>
            </span>
        </div>

        <div class="card-body">

            <?php // Inclui o arquivo que exibe mensagens de sucesso e erro
            include './app/adms/Views/partials/alerts.php';

            // var_dump($this->data['accessLevelsPages']);

            // var_dump($this->data['pages']);

            // Verifica se há páginas no array
            if ($this->data['pages'] ?? false) {
                // Ordenar páginas pelo grupo (agp_name)
                usort($this->data['pages'], function($a, $b) {
                    return strcmp($a['agp_name'], $b['agp_name']);
                });
?>

                <form action="<?php echo $_ENV['URL_ADM']; ?>list-access-levels-permissions/<?php echo $this->data['accessLevel']['id'] ?? ''; ?>" method="POST">

                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <input type="hidden" name="adms_access_level_id" value="<?php echo ($this->data['accessLevel']['id'] ?? ''); ?>">

                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Liberado</th>
                                <th scope="col">Página</th>
                                <th scope="col">Nome</th>
                                <th scope="col">Grupo</th>
                                <th scope="col" class="d-none d-md-table-cell">Observação</th>
                                <th scope="col" class="d-none d-md-table-cell">Pública / Privada</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            // Percorre o array de páginas
                            $isSuperAdmin = ($this->data['accessLevel']['id'] ?? null) == 1;
                            foreach ($this->data['pages'] as $page) {
                                // Extrai variáveis do array de página
                                extract($page); ?>
                                <tr>
                                    <td>
                                        <?php
                                        // Verifica se a página atual ($id) está no array de páginas
                                        $accessLevelsPages = $this->data['accessLevelsPages'] ? $this->data['accessLevelsPages'] : [];
                                        $checked = $isSuperAdmin ? 'checked' : (in_array($id, $accessLevelsPages) || $public_page ? 'checked' : '');
                                        $disabled = $isSuperAdmin || $public_page ? 'disabled' : '';
                                        echo "<div class='form-check form-switch'>";
                                        echo "<input type='checkbox' name='accessLevelPage[$id]' class='form-check-input' role='switch' id='accessLevelPage$id' value='$id' $checked $disabled>";
                                        echo "<label class='form-check-label' for='accessLevelPage$id'></label>";
                                        echo "</div>";
                                        ?>
                                    </td>
                                    <td><?php echo $id; ?></td>
                                    <td><?php echo $name; ?></td>
                                    <td><?php echo $agp_name; ?></td>
                                    <td class="d-none d-md-table-cell"><?php echo $obs; ?></td>
                                    <td class="d-none d-md-table-cell">
                                        <?php echo $public_page ? "<span class='badge text-bg-success'>Pública</span>" : "<span class='badge text-bg-danger'>Privada</span>";; ?>
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>
                    </table>


                    <div class="col-12">
                        <!-- <button type="submit" class="btn btn-warning btn-sm" onclick="showLoading()">Salvar</button> -->
                        <button type="submit" class="btn btn-warning btn-sm">Salvar</button>
                    </div>

                </form>
            <?php
            } else { // Exibe mensagem se nenhuma página for encontrada
                echo "<div class='alert alert-danger' role='alert'>Nenhuma página encontrada!</div>";
            } ?>

        </div>

    </div>
</div>
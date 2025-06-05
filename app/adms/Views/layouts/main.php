<?php
// Supondo que o .env já esteja carregado via alguma lib tipo vlucas/phpdotenv
$urlAdm = getenv('URL_ADM');
?>
<!DOCTYPE html>
<html lang="<?php echo $_ENV['APP_LOCALE']; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="<?php echo $_ENV['URL_ADM']; ?>public/adms/image/icon/favicon.ico">

    <!-- <link rel="stylesheet" href="<?php echo $_ENV['URL_ADM'] ?>public/adms/css/custom_adms.css"> -->

    <link rel="stylesheet" href="<?php echo $_ENV['URL_ADM'] ?>public/adms/css/sbadmin.css">

    <link rel="stylesheet" href="<?php echo $_ENV['URL_ADM'] ?>public/adms/css/bootstrap.min.css">

    <link rel="stylesheet" href="<?php echo $_ENV['URL_ADM'] ?>public/adms/css/styles_admin.css">

    <link rel="stylesheet" href="<?php echo $_ENV['URL_ADM'] ?>public/adms/DataTables/datatables.min.css">

    <script src="https://use.fontawesome.com/releases/v6.6.0/js/all.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

 




    <!-- JQ por CDN -->
    <!-- <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script> -->

    <!-- JQuery local -->
    <script src="<?php echo $_ENV['URL_ADM'] ?>public/adms/jquery/jquery-3.7.1.min.js"></script>


    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->



    <title>
        <?php
        echo $_ENV['APP_NAME'] . " - " . ($this->data['title_head'] ?? "");
        ?>
    </title>


</head>

<body class="sb-nav-fixed">

    <?php include 'app/adms/Views/partials/navbar.php'; ?>

    <div id="layoutSidenav">

        <?php include 'app/adms/Views/partials/menu.php'; ?>

        <div id="layoutSidenav_content">
            <main>

                <?php

                // Inclui o conteúdo principal da página, que é especificado pela propriedade $this->view. Este arquivo é dinâmico e pode variar conforme a lógica do controlador ou o contexto da página.
                include $this->view;

                ?>
            </main>

            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; <?php echo $_ENV['APP_NAME'] . " " . date("Y"); ?></div>
                        <div>
                            <a href="#" class="text-decoration-none">Política de Privacidade</a>
                            &middot;
                            <a href="#" class="text-decoration-none">Termos de Uso</a>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <script src="<?php echo $_ENV['URL_ADM'] ?>public/adms/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo $_ENV['URL_ADM'] ?>public/adms/js/sbadmin.js"></script>
    <script src="<?php echo $_ENV['URL_ADM'] ?>public/adms/js/script_admin.js"></script>

    <script src="<?php echo $_ENV['URL_ADM'] ?>public/adms/DataTables/datatables.min.js"></script>

    <script src="<?php echo $_ENV['URL_ADM'] ?>public/adms/js/telefone-mascara.js"></script>

    <script src="<?php echo $_ENV['URL_ADM'] ?>public/adms/js/mascaras.js"></script>

    <!-- Ajax para funcionar Mascaras JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>

    <!-- Bootstrap Bundle com Popper.js -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->


</body>

</html>
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-nav">
    <a class="navbar-brand ps-3" href="<?php echo $_ENV['URL_ADM']; ?>dashboard">Tiaraju</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        
    </form>
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item" href="<?php echo $_ENV['URL_ADM']; ?>profile">
                        <i class="fa-solid fa-user-pen"></i> Perfil
                    </a>
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                    <a class="dropdown-item" href="<?php echo $_ENV['URL_ADM']; ?>logout">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Sair
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
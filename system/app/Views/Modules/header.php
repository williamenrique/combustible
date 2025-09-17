<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" href="<?= IMG ?>favicon.ico">
		<title><?= $data['page_tag']?></title>
		<link rel="icon" type="image/png" href="<?= IMG ?>gas.svg">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
		<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
		<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
		<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css">
		<!-- <link rel="stylesheet" href="<?= PLUGINS ?>css/sweetalert2.css"> -->
		
		<link rel="stylesheet" href="<?= CSS ?>style.css">
		<link rel="stylesheet" href="<?= CSS ?>menu.css">
	</head>

	<body>
		<!-- inicio del contenedor total -->
		<div class="dashboard-container">
			<!-- Sidebar -->
			<aside class="sidebar" id="sidebar">
				<div class="toggle-sidebar-btn" id="toggleSidebar">
					<i class="fas fa-chevron-left"></i>
				</div>
				
				<div class="sidebar-header">
					<div class="user-image">
						<img src="<?= BASE_URL().''. $_SESSION['userData']['usuario_imagen']?>" alt="Usuario">
					</div>
					<div class="user-info">
						<h3 class="user-name"><?=  $_SESSION['userData']['usuario_nombres'].' '.$_SESSION['userData']['usuario_apellidos']?></h3>
						<p class="user-status">En línea</p>
					</div>
				</div>
			<?= cargar_menu_usuarios($_SESSION['userData']['usuario_nick'])?>
			</aside>
         	<!-- end Sidebar -->
			<!-- Main Content -->
			<main class="main-content" id="mainContent">
				 <!-- Top Bar -->
				<header class="top-bar">
					<button class="mobile-toggle" id="mobileToggle">
						<span></span>
						<span></span>
						<span></span>
					</button>
					
					<div class="top-bar-info">
						<span>Bienvenido, hoy es <strong id="current-date"></strong></span>
					</div>
					
					<div class="top-bar-actions">
						<div class="theme-toggle light" id="themeToggle">
							<i class="fas fa-sun"></i>
							<i class="fas fa-moon"></i>
							<span class="theme-toggle-handle"></span>
						</div>
						
						<div class="user-menu">
							<button class="user-menu-btn" id="userMenuBtn">
								<img src="<?= BASE_URL().''. $_SESSION['userData']['usuario_imagen']?>" alt="Usuario">
								<span><?= $_SESSION['userData']['usuario_nick']?></span>
							</button>
							<div class="user-menu-dropdown" id="userMenuDropdown">
								<a href="<?= BASE_URL() ?>user/perfil" class="dropdown-item"><i class="fas fa-user"></i> Mi Perfil</a>
								<a href="#" class="dropdown-item"><i class="fas fa-key"></i> Cambiar Contraseña</a>
								<a href="<?= BASE_URL() ?>logout" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
							</div>
						</div>
					</div>
				</header>
				 <!-- end Top Bar -->


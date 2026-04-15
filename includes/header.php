<?php
/**
 * Componente de cabecera y navegacion comun a todas las paginas.
 *
 * Incluye la barra de navegacion con el icono Pokeball y enlaces
 * a las secciones principales de la Pokedex.
 *
 * @param string $paginaActual Nombre de la pagina activa para resaltar en el menu.
 * @param string $tituloPagina Titulo mostrado en la pestaña del navegador.
 */

$paginaActual = $paginaActual ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloPagina ?? 'Pokedex' ?> — Pokedex</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-inner">
        <a href="index.php" class="navbar-brand">
            <span class="pokeball-icon"></span>
            Pokedex
        </a>
        <ul class="navbar-nav">
            <li><a href="index.php" class="<?= $paginaActual === 'inicio' ? 'active' : '' ?>">Inicio</a></li>
            <li><a href="buscar.php" class="<?= $paginaActual === 'buscar' ? 'active' : '' ?>">Buscar</a></li>
        </ul>
    </div>
</nav>

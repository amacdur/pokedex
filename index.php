<?php
/**
 * Página principal de la Pokedex.
 *
 * Muestra un listado paginado de pokemon obtenidos desde la pokeapi,
 * con sus sprites, nombres y tipos. Permite navegar entre paginas
 * para explorar todos los pokemon disponibles.
 *
 * @package Pokedex
 */

require_once 'PokeApiService.php';

$tituloPagina = 'Inicio';
$paginaActual = 'inicio';

$api           = new PokeApiService();
$porPagina     = 24;
$paginaActNum  = max(1, intval($_GET['page'] ?? 1));
$offset        = ($paginaActNum - 1) * $porPagina;
$error         = null;
$pokemonList   = [];
$total         = 0;

// Obtener lista
$respuesta = $api->obtenerListaPokemon($porPagina, $offset);

if ($respuesta === null) {
    $error = 'No se pudieron obtener los datos de la pokeapi. intentalo de nuevo mas tarde.';
} else {
    $total       = $respuesta['count'] ?? 0;
    $resultados  = $respuesta['results'] ?? [];

    // Obtener detalles de cada Pokemon
    $pokemonList = $api->obtenerDetallesMultiples($resultados);
}

$totalPaginas = ceil($total / $porPagina);

include 'includes/header.php';
?>

<div class="container">

    <section class="hero">
        <h1>Pokedex</h1>
        <p>Explora el mundo pokemon con datos en tiempo real desde la pokeapi. Descubre tipos, estadisticas y habilidades.</p>
    </section>

    <?php if ($total > 0): ?>
    <div class="stats-bar">
        <div class="stat-item">
            <div class="stat-value"><?= number_format($total) ?></div>
            <div class="stat-label">pokemon</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">18</div>
            <div class="stat-label">Tipos</div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error-box">
            <p>⚠️ <?= htmlspecialchars($error) ?></p>
        </div>
    <?php elseif (!empty($pokemonList)): ?>

        <div class="pokemon-grid">
            <?php foreach ($pokemonList as $pokemon): ?>
                <?php include 'includes/pokemon_card.php'; ?>
            <?php endforeach; ?>
        </div>

        <!-- Paginación -->
        <div class="pagination">
            <a href="index.php?page=<?= $paginaActNum - 1 ?>"
               class="page-btn <?= $paginaActNum <= 1 ? 'disabled' : '' ?>">
                ← Anterior
            </a>
            <span class="page-info">Página <?= $paginaActNum ?> de <?= $totalPaginas ?></span>
            <a href="index.php?page=<?= $paginaActNum + 1 ?>"
               class="page-btn <?= $paginaActNum >= $totalPaginas ? 'disabled' : '' ?>">
                Siguiente →
            </a>
        </div>

    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>

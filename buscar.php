<?php
/**
 * Página de busqueda de pokemon por nombre o ID.
 *
 * Permite al usuario introducir el nombre exacto o el ID numerico de
 * un pokemon para consultar sus datos desde la pokeapi.
 *
 * @package Pokedex
 */

require_once 'PokeApiService.php';

$tituloPagina = 'Buscar pokemon';
$paginaActual = 'buscar';

$api       = new PokeApiService();
$consulta  = trim($_GET['q'] ?? '');
$pokemon   = null;
$error     = null;

if (!empty($consulta)) {
    $pokemon = $api->obtenerPokemon($consulta);

    if ($pokemon === null) {
        $error = "No se encontró ningún pokemon con el nombre o ID \"{$consulta}\".";
    }
}

include 'includes/header.php';
?>

<div class="container">

    <section class="hero">
        <h1>Buscar pokemon</h1>
        <p>Introduce el nombre exacto o el numero de un pokemon para ver su ficha completa.</p>
    </section>

    <div class="search-section">
        <span class="search-icon">🔍</span>
        <form method="get" action="buscar.php">
            <input type="text"
                   name="q"
                   class="search-input"
                   placeholder="Nombre o ID... (ej: pikachu, 25, charizard)"
                   value="<?= htmlspecialchars($consulta) ?>"
                   autofocus>
        </form>
    </div>

    <?php if ($error): ?>
        <div class="no-results">
            <div class="emoji">❌</div>
            <p><?= htmlspecialchars($error) ?></p>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.5rem;">
                Recuerda: la busqueda usa el nombre en ingles (ej: "pikachu", "bulbasaur", "mewtwo").
            </p>
        </div>
    <?php elseif ($pokemon): ?>
        <div class="pokemon-grid" style="max-width: 280px; margin: 0 auto;">
            <?php include 'includes/pokemon_card.php'; ?>
        </div>
        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="detalle.php?id=<?= $pokemon['id'] ?>" class="page-btn">
                Ver ficha completa →
            </a>
        </div>
    <?php elseif (empty($consulta)): ?>
        <div class="no-results">
            <div class="emoji">🔎</div>
            <p>Introduce un nombre o ID para buscar un pokemon.</p>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>

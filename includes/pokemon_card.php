<?php
/**
 * Componente reutilizable para renderizar una tarjeta de pokemon.
 *
 * Recibe un array con los datos completos de un pokemon (obtenidos del
 * endpoint /pokemon/{id}) y genera el HTML de la tarjeta visual.
 *
 * @param array $pokemon Datos completos del pokemon devueltos por la API.
 */

$nombre  = htmlspecialchars($pokemon['name'] ?? 'desconocido');
$id      = $pokemon['id'] ?? 0;
$sprite  = PokeApiService::obtenerSprite($pokemon);
$tipos   = PokeApiService::obtenerTipos($pokemon);
$numStr  = PokeApiService::formatearId($id);
$colorAccent = $tipos[0] ?? 'normal';
?>
<a href="detalle.php?id=<?= $id ?>" class="pokemon-card" style="--card-accent: var(--type-<?= $colorAccent ?>);">
    <div class="card-img-wrapper">
        <span class="pokemon-number"><?= $numStr ?></span>
        <?php if ($sprite): ?>
            <img src="<?= htmlspecialchars($sprite) ?>" alt="<?= $nombre ?>" loading="lazy">
        <?php endif; ?>
    </div>
    <div class="card-body">
        <h3><?= $nombre ?></h3>
        <div class="card-types">
            <?php foreach ($tipos as $tipo): ?>
                <span class="type-badge type-<?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($tipo) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</a>

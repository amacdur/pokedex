<?php
/**
 * Página de detalle de un pokemon.
 *
 * Muestra toda la informacion disponible sobre un pokemon especifico:
 * sprite oficial, tipos, estadisticas base con barras visuales,
 * habilidades, medidas fisicas y descripcion de la especie.
 *
 * @package Pokedex
 */

require_once 'PokeApiService.php';

$api     = new PokeApiService();
$idParam = $_GET['id'] ?? '';
$pokemon = null;
$especie = null;
$error   = null;

if (empty($idParam)) {
    $error = 'No se ha especificado un pokemon.';
} else {
    $pokemon = $api->obtenerPokemon($idParam);

    if ($pokemon === null) {
        $error = 'No se pudo obtener la informacion del pokemon.';
    } else {
        // Obtener datos de especie (descripcion)
        $especie = $api->obtenerEspecie($pokemon['id']);
    }
}

$tituloPagina = $pokemon ? ucfirst($pokemon['name'] ?? 'Detalle') : 'Error';
$paginaActual = '';

include 'includes/header.php';
?>

<div class="detail-container">

    <a href="javascript:history.back()" class="back-btn">← Volver</a>

    <?php if ($error): ?>
        <div class="error-box">
            <p>⚠️ <?= htmlspecialchars($error) ?></p>
        </div>
    <?php elseif ($pokemon): ?>
        <?php
            $nombre      = htmlspecialchars($pokemon['name']);
            $id          = $pokemon['id'];
            $numStr      = PokeApiService::formatearId($id);
            $sprite      = PokeApiService::obtenerSprite($pokemon);
            $tipos       = PokeApiService::obtenerTipos($pokemon);
            $stats       = PokeApiService::obtenerEstadisticas($pokemon);
            $habilidades = PokeApiService::obtenerHabilidades($pokemon);
            $altura      = PokeApiService::formatearAltura($pokemon['height'] ?? 0);
            $peso        = PokeApiService::formatearPeso($pokemon['weight'] ?? 0);
            $experiencia = $pokemon['base_experience'] ?? 'N/A';
            $descripcion = $especie ? PokeApiService::obtenerDescripcion($especie) : 'Descripción no disponible.';

            // Nombres de stats para mostrar
            $nombresStats = [
                'hp'              => 'HP',
                'attack'          => 'Ataque',
                'defense'         => 'Defensa',
                'special-attack'  => 'At. Especial',
                'special-defense' => 'Def. Especial',
                'speed'           => 'Velocidad',
            ];
        ?>

        <div class="detail-header">
            <div class="detail-img-box">
                <?php if ($sprite): ?>
                    <img src="<?= htmlspecialchars($sprite) ?>" alt="<?= $nombre ?>">
                <?php endif; ?>
            </div>

            <div class="detail-info">
                <span class="detail-number"><?= $numStr ?></span>
                <h1><?= $nombre ?></h1>

                <div class="detail-types">
                    <?php foreach ($tipos as $tipo): ?>
                        <span class="type-badge type-<?= htmlspecialchars($tipo) ?>">
                            <?= htmlspecialchars($tipo) ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <p style="color: var(--text-secondary); font-size: 0.92rem; margin-bottom: 1.5rem; line-height: 1.6;">
                    <?= htmlspecialchars($descripcion) ?>
                </p>

                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="label">Altura</div>
                        <div class="value"><?= $altura ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Peso</div>
                        <div class="value"><?= $peso ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Experiencia Base</div>
                        <div class="value"><?= $experiencia ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Nº Pokedex</div>
                        <div class="value"><?= $numStr ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- estadisticas base -->
        <div class="stats-section">
            <h2>estadisticas Base</h2>
            <?php foreach ($stats as $statKey => $statVal): ?>
                <?php
                    $porcentaje = min(100, ($statVal / 255) * 100);
                    $clase = 'low';
                    if ($statVal >= 80) $clase = 'mid';
                    if ($statVal >= 120) $clase = 'high';
                    $labelStat = $nombresStats[$statKey] ?? ucfirst($statKey);
                ?>
                <div class="stat-row">
                    <span class="stat-name"><?= htmlspecialchars($labelStat) ?></span>
                    <span class="stat-num"><?= $statVal ?></span>
                    <div class="stat-bar-bg">
                        <div class="stat-bar-fill <?= $clase ?>"
                             style="width: <?= round($porcentaje) ?>%;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Habilidades -->
        <div class="detail-section">
            <h2>Habilidades</h2>
            <div class="tag-list">
                <?php foreach ($habilidades as $hab): ?>
                    <span class="tag"><?= htmlspecialchars($hab) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Movimientos (primeros 15) -->
        <?php if (isset($pokemon['moves']) && !empty($pokemon['moves'])): ?>
        <div class="detail-section">
            <h2>Algunos Movimientos</h2>
            <div class="tag-list">
                <?php
                    $movimientos = array_slice($pokemon['moves'], 0, 15);
                    foreach ($movimientos as $mov):
                        $nombreMov = $mov['move']['name'] ?? '';
                ?>
                    <span class="tag"><?= htmlspecialchars($nombreMov) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Navegacion entre pokemon -->
        <div class="pagination" style="margin-top: 2.5rem;">
            <?php if ($id > 1): ?>
                <a href="detalle.php?id=<?= $id - 1 ?>" class="page-btn">
                    ← <?= PokeApiService::formatearId($id - 1) ?>
                </a>
            <?php else: ?>
                <span class="page-btn disabled">← Anterior</span>
            <?php endif; ?>

            <span class="page-info"><?= $numStr ?></span>

            <a href="detalle.php?id=<?= $id + 1 ?>" class="page-btn">
                <?= PokeApiService::formatearId($id + 1) ?> →
            </a>
        </div>

    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>

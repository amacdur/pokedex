<?php
/**
 * PokeApiService - Clase de servicio para consumir la API REST de pokeapi.
 *
 * Esta clase encapsula toda la logica de comunicacion con la API publica
 * https://pokeapi.co, proporcionando metodos para obtener informacion
 * detallada sobre pokemon en formato JSON.
 *
 * La pokeapi es una API RESTful gratuita que no requiere autenticacion
 * y ofrece datos completos del universo pokemon.
 *
 * @author    Ainhoa Macias Duran
 * @version   1.0.0
 * @package   Pokedex
 * @link      https://pokeapi.co
 */
class PokeApiService
{
    /** @var string URL base de la API REST de pokeapi v2 */
    private string $baseUrl = 'https://pokeapi.co/api/v2';

    /** @var int Tiempo maximo de espera para las peticiones en segundos */
    private int $timeout = 15;

    /**
     * Realiza una peticion HTTP GET a la URL indicada y devuelve el resultado
     * decodificado desde JSON.
     *
     * Utiliza cURL para la comunicacion HTTP. Si la peticion falla o el
     * código de respuesta no es 200, devuelve null.
     *
     * @param  string     $url URL completa a la que realizar la peticion GET.
     * @return array|null Array asociativo con los datos JSON o null si hay error.
     */
    private function hacerPeticion(string $url): ?array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $respuesta  = curl_exec($ch);
        $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error      = curl_error($ch);
        curl_close($ch);

        if ($error || $codigoHttp !== 200) {
            return null;
        }

        $datos = json_decode($respuesta, true);

        return is_array($datos) ? $datos : null;
    }

    /**
     * Obtiene un listado paginado de pokemon desde la API.
     *
     * La API devuelve un objeto con los campos 'count', 'next', 'previous'
     * y 'results'. Cada resultado contiene 'name' y 'url'.
     *
     * @param  int        $limite  Número de pokemon por pagina (max. recomendado: 24).
     * @param  int        $offset  Desplazamiento desde el inicio de la lista.
     * @return array|null Array con la respuesta paginada o null si hay error.
     */
    public function obtenerListaPokemon(int $limite = 24, int $offset = 0): ?array
    {
        $url = "{$this->baseUrl}/pokemon?limit={$limite}&offset={$offset}";

        return $this->hacerPeticion($url);
    }

    /**
     * Obtiene los datos detallados de un pokemon especifico a partir de
     * su nombre o su ID numerico.
     *
     * Incluye tipos, estadisticas base, habilidades, sprites y mas.
     *
     * @param  string|int $identificador Nombre (en minúsculas) o ID del pokemon.
     * @return array|null Array con toda la informacion del pokemon o null si no se encuentra.
     */
    public function obtenerPokemon($identificador): ?array
    {
        $identificador = strtolower(trim((string) $identificador));

        if (empty($identificador)) {
            return null;
        }

        $url = "{$this->baseUrl}/pokemon/{$identificador}";

        return $this->hacerPeticion($url);
    }

    /**
     * Obtiene la informacion de la especie de un pokemon, que incluye
     * datos como la descripcion (flavor text), tasa de captura,
     * cadena evolutiva, hábitat y color.
     *
     * @param  int        $id ID numerico del pokemon.
     * @return array|null Array con los datos de la especie o null si hay error.
     */
    public function obtenerEspecie(int $id): ?array
    {
        $url = "{$this->baseUrl}/pokemon-species/{$id}";

        return $this->hacerPeticion($url);
    }

    /**
     * Obtiene los datos detallados de multiples pokemon a partir de una
     * lista de resultados basicos (nombre y URL) devueltos por la API.
     *
     * Este método realiza multiples peticiones para obtener los detalles
     * completos de cada pokemon del listado.
     *
     * @param  array $listaBasica Array de resultados con claves 'name' y 'url'.
     * @return array Array de pokemon con datos detallados completos.
     */
    public function obtenerDetallesMultiples(array $listaBasica): array
    {
        $pokemonDetallados = [];

        foreach ($listaBasica as $item) {
            $datos = $this->hacerPeticion($item['url']);

            if ($datos !== null) {
                $pokemonDetallados[] = $datos;
            }
        }

        return $pokemonDetallados;
    }

    /**
     * Extrae la URL del sprite oficial de un pokemon a partir de sus datos.
     *
     * Prioriza el artwork oficial; si no esta disponible, utiliza el
     * sprite frontal por defecto.
     *
     * @param  array  $pokemon Array con los datos del pokemon.
     * @return string URL de la imagen del pokemon o cadena vacía si no hay sprite.
     */
    public static function obtenerSprite(array $pokemon): string
    {
        // Artwork oficial (mejor calidad)
        $artwork = $pokemon['sprites']['other']['official-artwork']['front_default'] ?? '';

        if (!empty($artwork)) {
            return $artwork;
        }

        // Sprite por defecto como alternativa
        return $pokemon['sprites']['front_default'] ?? '';
    }

    /**
     * Extrae los nombres de los tipos de un pokemon como un array simple
     * de cadenas de texto.
     *
     * @param  array $pokemon Array con los datos del pokemon.
     * @return array Array de strings con los nombres de los tipos (ej: ["fire", "flying"]).
     */
    public static function obtenerTipos(array $pokemon): array
    {
        $tipos = [];

        if (isset($pokemon['types']) && is_array($pokemon['types'])) {
            foreach ($pokemon['types'] as $tipo) {
                $tipos[] = $tipo['type']['name'] ?? '';
            }
        }

        return array_filter($tipos);
    }

    /**
     * Extrae las estadisticas base de un pokemon y las devuelve como un
     * array asociativo con el nombre de la estadística como clave.
     *
     * @param  array $pokemon Array con los datos del pokemon.
     * @return array Array asociativo [nombre_stat => valor] (ej: ["hp" => 45, "attack" => 49]).
     */
    public static function obtenerEstadisticas(array $pokemon): array
    {
        $stats = [];

        if (isset($pokemon['stats']) && is_array($pokemon['stats'])) {
            foreach ($pokemon['stats'] as $stat) {
                $nombre        = $stat['stat']['name'] ?? 'unknown';
                $valor         = $stat['base_stat'] ?? 0;
                $stats[$nombre] = $valor;
            }
        }

        return $stats;
    }

    /**
     * Extrae los nombres de las habilidades de un pokemon.
     *
     * @param  array $pokemon Array con los datos del pokemon.
     * @return array Array de strings con los nombres de las habilidades.
     */
    public static function obtenerHabilidades(array $pokemon): array
    {
        $habilidades = [];

        if (isset($pokemon['abilities']) && is_array($pokemon['abilities'])) {
            foreach ($pokemon['abilities'] as $hab) {
                $habilidades[] = $hab['ability']['name'] ?? '';
            }
        }

        return array_filter($habilidades);
    }

    /**
     * Extrae la descripcion (flavor text) en espaNol de los datos de especie.
     *
     * Si no hay descripcion en espaNol, devuelve la primera en ingles.
     * Si no hay ninguna, devuelve un mensaje por defecto.
     *
     * @param  array  $especie Array con los datos de la especie del pokemon.
     * @return string Descripcion del pokemon en texto plano.
     */
    public static function obtenerDescripcion(array $especie): string
    {
        $textos = $especie['flavor_text_entries'] ?? [];

        // Intentar primero en espaNol
        foreach ($textos as $entry) {
            if (($entry['language']['name'] ?? '') === 'es') {
                return str_replace(["\n", "\f", "\r"], ' ', $entry['flavor_text']);
            }
        }

        // Fallback a ingles
        foreach ($textos as $entry) {
            if (($entry['language']['name'] ?? '') === 'en') {
                return str_replace(["\n", "\f", "\r"], ' ', $entry['flavor_text']);
            }
        }

        return 'Descripción no disponible.';
    }

    /**
     * Formatea el ID de un pokemon con ceros a la izquierda para mantener
     * un formato visual consistente (ej: #001, #025, #150).
     *
     * @param  int    $id ID numerico del pokemon.
     * @return string ID formateado con prefijo # y ceros a la izquierda.
     */
    public static function formatearId(int $id): string
    {
        return '#' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Convierte la altura del pokemon de decimetros (unidad de la API)
     * a metros, con un decimal.
     *
     * @param  int    $altura Altura en decimetros proporcionada por la API.
     * @return string Altura formateada en metros (ej: "1.7 m").
     */
    public static function formatearAltura(int $altura): string
    {
        return number_format($altura / 10, 1) . ' m';
    }

    /**
     * Convierte el peso del pokemon de hectogramos (unidad de la API)
     * a kilogramos, con un decimal.
     *
     * @param  int    $peso Peso en hectogramos proporcionado por la API.
     * @return string Peso formateado en kilogramos (ej: "6.9 kg").
     */
    public static function formatearPeso(int $peso): string
    {
        return number_format($peso / 10, 1) . ' kg';
    }

}

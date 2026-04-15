# 🔴 Pokedex App

Aplicación web PHP que consume la API REST publica [pokeapi](https://pokeapi.co) para mostrar informacion detallada sobre pokemon: tipos, estadisticas, habilidades, sprites y descripciones.

**Tarea RA9 — Desarrollo Web en Entorno Servidor**

## Funcionalidades

- **Listado paginado** de pokemon con sprites oficiales y tipos
- **Búsqueda** por nombre exacto o ID numerico
- **Ficha de detalle** con estadisticas base (barras visuales), habilidades, movimientos, descripción en espaNol, altura y peso
- **Navegación** entre pokemon anterior/siguiente desde la ficha
- **Diseño responsive** con temática retro-gaming

## API Utilizada

**pokeapi v2** — https://pokeapi.co

API REST publica y gratuita. No requiere autenticacion ni API key.
Referencia obtenida de: https://desarrolloweb.com/colecciones/api-rest-uso-publico-libre

## Estructura del proyecto

```
pokedex-app/
├── index.php              # Listado paginado
├── buscar.php             # Buscador
├── detalle.php            # Ficha completa
├── PokeApiService.php     # Clase de servicio (PHPDoc)
├── includes/
│   ├── header.php         # Cabecera común
│   ├── footer.php         # Pie de página
│   └── pokemon_card.php   # Tarjeta reutilizable
├── css/style.css          # Estilos
├── jmeter/
│   └── pokedex-test.jmx   # Plan de pruebas JMeter
└── README.md
```
## Pruebas JMeter

Configuración Concurrency Thread Group:
- Target Concurrency: 200 usuarios
- Ramp Up Time: 10 min
- Ramp-Up Steps: 10
- Hold Target Rate Time: 5 min

Archivo incluido: `jmeter/pokedex-test.jmx`

## Licencia

Proyecto académico — Uso educativo.

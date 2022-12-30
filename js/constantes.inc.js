console.log("constantes.inc.js");
// Constantes (NO modificar durante la ejecucion de la aplicacion)

var SIN_SEGUIR_MOVIL = -1;

var DATAMOVIL_MAX_LENGTH = 14;
var MATRICULA_MAX_LENGTH = 14; // Longitud maxima (en caracteres) de la matricula del movil
var TIPOEVENTO_MAX_LENGTH = 20; // Longitud maxima (en caracteres) del tipo de reporte
var NOMBREEMPRESA_MAX_LENGTH = 20; // Longitud maxima (en caracteres) del nombre del transportista
var NOMBREGRUPO_MAX_LENGTH = 25; // Longitud maxima (en caracteres) del nombre del transportista
var LATLNG_MAX_LENGTH = 13;

var ONE_MILLISECOND   = 1;
var ONE_SECOND        = 1000 * ONE_MILLISECOND;
var ONE_MINUTE        = 60   * ONE_SECOND;
var ONE_HOUR          = 60   * ONE_MINUTE;

var TRACE_REFRESH_INTERVAL = 60 * ONE_SECOND; // Refresco S.I.R., normalmente 60 segundos
var ALERTAS_REFRESH_INTERVAL = 30 * ONE_SECOND; // Refresco grilla de alertas, normalmente 60 segundos
var PIP_REFRESH_INTERVAL = 10 * ONE_SECOND; // Refresco Picture in Picture

var ORDERING_CRITERIA = {
    "GROUP": 0,
    "CLIENT": 1,
    "EQUIPMENT_MODEL": 2,
    "CELL_COMPANY": 3,
	"LOGISTICA": 4
};

var MOVIL_VIEWS = {
    "INTERNO": 1,
    "MATRICULA": 2,
    "OTROS": 3
};

var SEARCH_TYPES = {
    "FILTER_ONLY": 1,
    "FILTER_AND_CHASE": 2
};

var ABREV_RUMBO = {
    "norte": "N",
    "noreste": "NE",
    "este": "E",
    "sudeste": "SE",
    "sur": "S",
    "sudoeste": "SO",
    "oeste": "O",
    "noroeste": "NO"
};

// Teclas

var KEYS = {
    "ENTER": 13,
    "SHIF": 16,
    "CTRL": 17
}
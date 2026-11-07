#!/usr/bin/env bash
# ============================================================
# probar_api.sh — Pruebas con curl de los endpoints JSON del TP9
# Uso:  bash docs/tp9/probar_api.sh [puerto]
#       PUERTO=8091 ADMIN_EMAIL=... ADMIN_PASS=... bash docs/tp9/probar_api.sh
# Requiere el sitio levantado con: php -S localhost:<puerto> (en la raíz del proyecto).
# Por cada caso imprime método, URL, código esperado/obtenido, Content-Type
# y un fragmento del cuerpo. Al final muestra el total de casos OK/FALLA.
# El caso 200 de turno_estado reenvía el MISMO estado que ya tiene el turno,
# así que no modifica datos de la base compartida.
# ============================================================
set -u

PUERTO="${1:-${PUERTO:-8091}}"
BASE="http://localhost:${PUERTO}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@boxly.com.ar}"
ADMIN_PASS="${ADMIN_PASS:-Boxly2026!}"

TMP="$(mktemp -d)"
COOKIES="$TMP/cookies.txt"
trap 'rm -rf "$TMP"' EXIT

OK=0
FALLAS=0

# probar <descripcion> <codigo esperado> <metodo> <ruta> [args extra de curl...]
probar() {
  local desc="$1" esperado="$2" metodo="$3" ruta="$4"
  shift 4
  local salida codigo tipo cuerpo resultado
  salida="$(curl -s -o "$TMP/cuerpo" -w '%{http_code}|%{content_type}' \
    -X "$metodo" "$@" "${BASE}${ruta}")"
  codigo="${salida%%|*}"
  tipo="${salida#*|}"
  cuerpo="$(head -c 160 "$TMP/cuerpo" | tr '\n\r' '  ')"

  # SIN_JSON=1 solo exige el código (respuestas que no genera la API, ej. 404 del servidor)
  if [ "$codigo" = "$esperado" ] && { [ "${SIN_JSON:-0}" = 1 ] || [[ "$tipo" == application/json* ]]; }; then
    resultado="OK"
    OK=$((OK + 1))
  else
    resultado="FALLA"
    FALLAS=$((FALLAS + 1))
  fi

  printf '[%s] %s\n' "$resultado" "$desc"
  printf '      %s %s\n' "$metodo" "$ruta"
  printf '      esperado=%s obtenido=%s content-type=%s\n' "$esperado" "$codigo" "${tipo:-(ninguno)}"
  printf '      cuerpo: %s\n\n' "$cuerpo"
}

echo "== Probando API TP9 en $BASE =="
if ! curl -s -o /dev/null "$BASE/"; then
  echo "No responde $BASE. Levantá el servidor con: php -S localhost:$PUERTO" >&2
  exit 2
fi
echo

# ------------------------------------------------------------
# api/buscar_servicios.php (GET público)
# ------------------------------------------------------------
echo "--- api/buscar_servicios.php ---"
B="/api/buscar_servicios.php"
probar "Sin parámetro q: lista todos"              200 GET  "$B"
probar "Búsqueda con resultados (frenos)"           200 GET  "$B?q=frenos"
probar "Búsqueda con tilde (diagnóstico)"           200 GET  "$B?q=diagn%C3%B3stico"
probar "Búsqueda sin resultados (data vacía)"       200 GET  "$B?q=zzzzzz"
probar "Comodín % tratado como texto literal"       200 GET  "$B?q=%25"
probar "q vacío (solo espacios)"                    200 GET  "$B?q=%20%20"
probar "q como arreglo (q[]=x)"                     400 GET  "$B?q%5B%5D=x"
probar "q de más de 60 caracteres"                  400 GET  "$B?q=$(printf 'a%.0s' {1..61})"
probar "q con UTF-8 inválido (%FF)"                 400 GET  "$B?q=%FF"
probar "Método incorrecto (POST)"                   405 POST "$B"
probar "Método incorrecto (DELETE)"                 405 DELETE "$B"
SIN_JSON=1 probar "Ruta inexistente en api/ (404 del servidor)" 404 GET "/api/no_existe.php"

# ------------------------------------------------------------
# api/consultar_turno.php (GET público: id + patente)
# ------------------------------------------------------------
echo "--- api/consultar_turno.php ---"
C="/api/consultar_turno.php"
probar "Sin parámetros"                            400 GET "$C"
probar "Solo id (falta patente)"                  400 GET "$C?id=1"
probar "Solo patente (falta id)"                  400 GET "$C?patente=AC123BD"
probar "id no entero (abc)"                       400 GET "$C?id=abc&patente=AC123BD"
probar "id con decimales (1.5)"                   400 GET "$C?id=1.5&patente=AC123BD"
probar "id cero"                                  400 GET "$C?id=0&patente=AC123BD"
probar "id negativo"                              400 GET "$C?id=-3&patente=AC123BD"
probar "id como arreglo (id[]=1)"                 400 GET "$C?id%5B%5D=1&patente=AC123BD"
probar "patente con formato inválido"             400 GET "$C?id=1&patente=123"
probar "patente muy corta"                        400 GET "$C?id=1&patente=AB"
probar "Método incorrecto (POST)"                 405 POST "$C"
probar "Turno inexistente (id 999999)"            404 GET "$C?id=999999&patente=AC123BD"

# ------------------------------------------------------------
# api/turno_estado.php (POST JSON, solo Administrador)
# ------------------------------------------------------------
echo "--- api/turno_estado.php (sin sesión) ---"
T="/api/turno_estado.php"
JSON=(-H "Content-Type: application/json")
probar "Método incorrecto (GET)"                    405 GET  "$T"
probar "Sin sesión"                                 401 POST "$T" "${JSON[@]}" -d '{"id":1,"estado_id":1}'

echo "--- Login de administrador ($ADMIN_EMAIL) ---"
csrf_login="$(curl -s -c "$COOKIES" -b "$COOKIES" "$BASE/login.php" \
  | sed -n 's/.*name="csrf" value="\([^"]*\)".*/\1/p' | head -n1)"
curl -s -o /dev/null -c "$COOKIES" -b "$COOKIES" "$BASE/login.php" \
  --data-urlencode "csrf=$csrf_login" \
  --data-urlencode "email=$ADMIN_EMAIL" \
  --data-urlencode "password=$ADMIN_PASS"

panel="$(curl -s -c "$COOKIES" -b "$COOKIES" "$BASE/admin/index.php")"
csrf="$(printf '%s' "$panel" | sed -n 's/.*data-csrf="\([^"]*\)".*/\1/p' | head -n1)"
if [ -z "$csrf" ]; then
  echo "No se pudo iniciar sesión o leer data-csrf de admin/index.php: se omiten los casos con sesión." >&2
  FALLAS=$((FALLAS + 1))
else
  echo "Sesión iniciada; token CSRF obtenido del panel."
  # Primer turno de la tabla y el estado que tiene seleccionado
  turno_id="${TURNO_ID:-$(printf '%s' "$panel" | grep -o 'bx-estado-select[^>]*data-id="[0-9]*"' \
    | sed -n 's/.*data-id="\([0-9]*\)".*/\1/p' | head -n1)}"
  estado_id="${ESTADO_ID:-$(printf '%s' "$panel" | tr '\n' ' ' \
    | grep -o "data-id=\"$turno_id\"[^<]*>\( *<option[^>]*>[^<]*</option>\)*" \
    | grep -o 'value="[0-9]*" selected' | sed 's/[^0-9]//g' | head -n1)}"
  echo "Turno usado para el caso 200: id=${turno_id:-?} estado_id=${estado_id:-?}"
  echo

  # Primer turno del panel: id + patente de la MISMA fila, para consultar_turno
  fila_turno="$(printf '%s' "$panel" | tr '\n' ' ' \
    | sed 's/<tr>/\n/g; s/<\/tr>/\n/g' | grep 'bx-estado-select' | head -n1)"
  turno_consulta="${TURNO_CONSULTA:-$(printf '%s' "$fila_turno" | grep -o 'data-id="[0-9]*"' | head -n1 | sed 's/[^0-9]//g')}"
  patente_turno="${PATENTE_TURNO:-$(printf '%s' "$fila_turno" \
    | grep -oE '[A-Z]{3}[0-9]{3}|[A-Z]{2}[0-9]{3}[A-Z]{2}' | head -n1)}"

  echo "--- api/consultar_turno.php (con datos del panel) ---"
  if [ -n "$turno_consulta" ] && [ -n "$patente_turno" ]; then
    echo "Turno usado: id=$turno_consulta patente=$patente_turno"
    probar "Consulta válida"                       200 GET "$C?id=$turno_consulta&patente=$patente_turno"
    patente_lower="$(printf '%s' "$patente_turno" | tr 'A-Z' 'a-z')"
    probar "Patente en minúsculas (se normaliza)" 200 GET "$C?id=$turno_consulta&patente=$patente_lower"
    # Misma patente pero con la primera letra cambiada: formato válido, no coincide
    primera="$(printf '%s' "$patente_turno" | cut -c1)"
    nueva="Z"; [ "$primera" = "Z" ] && nueva="Y"
    patente_falsa="${nueva}${patente_turno:1}"
    probar "Patente incorrecta (formato válido)"   404 GET "$C?id=$turno_consulta&patente=$patente_falsa"
  else
    echo "[OMITIDO] No se encontró un turno con patente en el panel (definí TURNO_CONSULTA y PATENTE_TURNO)."
  fi
  echo

  AUT=(-b "$COOKIES" -H "Content-Type: application/json" -H "X-CSRF-Token: $csrf")
  if [ -n "${turno_id:-}" ] && [ -n "${estado_id:-}" ]; then
    probar "Cambio válido (mismo estado, no altera datos)" 200 POST "$T" "${AUT[@]}" \
      -d "{\"id\":$turno_id,\"estado_id\":$estado_id}"
  else
    echo "[OMITIDO] No se encontró un turno en el panel para el caso 200 (definí TURNO_ID y ESTADO_ID)."
    echo
  fi
  probar "Turno inexistente"                        404 POST "$T" "${AUT[@]}" -d '{"id":999999,"estado_id":1}'
  probar "Estado inexistente"                       400 POST "$T" "${AUT[@]}" -d "{\"id\":${turno_id:-1},\"estado_id\":9999}"
  probar "Falta estado_id"                          400 POST "$T" "${AUT[@]}" -d '{"id":1}'
  probar "id no entero (1.5)"                       400 POST "$T" "${AUT[@]}" -d '{"id":1.5,"estado_id":1}'
  probar "id negativo"                              400 POST "$T" "${AUT[@]}" -d '{"id":-3,"estado_id":1}'
  probar "id booleano"                              400 POST "$T" "${AUT[@]}" -d '{"id":true,"estado_id":1}'
  probar "JSON mal formado"                         400 POST "$T" "${AUT[@]}" -d '{"id":1,'
  probar "Arreglo JSON en lugar de objeto"          400 POST "$T" "${AUT[@]}" -d '[1,2]'
  probar "Content-Type form-urlencoded"             415 POST "$T" -b "$COOKIES" -H "X-CSRF-Token: $csrf" \
    -H "Content-Type: application/x-www-form-urlencoded" -d 'id=1&estado_id=1'
  probar "Sin cabecera X-CSRF-Token"                403 POST "$T" -b "$COOKIES" "${JSON[@]}" -d '{"id":1,"estado_id":1}'
  probar "X-CSRF-Token incorrecto"                  403 POST "$T" -b "$COOKIES" "${JSON[@]}" \
    -H "X-CSRF-Token: token-falso" -d '{"id":1,"estado_id":1}'
  probar "Método incorrecto con sesión (PUT)"       405 PUT  "$T" "${AUT[@]}" -d '{"id":1,"estado_id":1}'
fi

echo "== Resultado: $OK OK, $FALLAS FALLA(S) =="
[ "$FALLAS" -eq 0 ]

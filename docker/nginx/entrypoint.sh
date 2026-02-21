#!/bin/sh
set -e

# Значения по умолчанию — используются в local-окружении без .env.prod
API_DOMAIN="${API_DOMAIN:-localhost}"
API_PORT="${API_PORT:-8080}"
ENABLE_SSL="${ENABLE_SSL:-false}"
SSL_CERT="${SSL_CERT:-/etc/nginx/certs/fullchain.pem}"
SSL_KEY="${SSL_KEY:-/etc/nginx/certs/privkey.pem}"

TEMPLATE_DIR="/etc/nginx/templates"
OUTPUT="/etc/nginx/conf.d/api.conf"

# Выбираем шаблон в зависимости от режима SSL.
# ENABLE_SSL=true  → HTTPS + HTTP/2 + HTTP/3 + редирект с 80
# ENABLE_SSL=false → plain HTTP
if [ "$ENABLE_SSL" = "true" ]; then
    TEMPLATE="${TEMPLATE_DIR}/api.https.conf.template"
    echo "[nginx-entrypoint] SSL mode: HTTPS+HTTP2+HTTP3 on port ${API_PORT}, domain ${API_DOMAIN}"
else
    TEMPLATE="${TEMPLATE_DIR}/api.http.conf.template"
    echo "[nginx-entrypoint] Plain HTTP mode on port ${API_PORT}, domain ${API_DOMAIN}"
fi

# envsubst подставляет только наши переменные, не трогая nginx-переменные вида $uri, $host и т.д.
envsubst '${API_DOMAIN} ${API_PORT} ${SSL_CERT} ${SSL_KEY}' \
    < "$TEMPLATE" > "$OUTPUT"

echo "[nginx-entrypoint] Config written to ${OUTPUT}"

exec "$@"

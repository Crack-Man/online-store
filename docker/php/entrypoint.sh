#!/usr/bin/env bash
set -e

WWW_UID="${WWW_UID:-33}"
WWW_GID="${WWW_GID:-33}"

mkdir -p /srv/api/storage/logs /srv/api/bootstrap/cache

# Передаем права на каталоги нужному пользователю, чтобы приложение могло писать логи и кэш.
# || true — игнорируем ошибку, если каталог уже принадлежит нужному пользователю (например, при повторном старте).
chown -R "${WWW_UID}:${WWW_GID}" /srv/api/storage /srv/api/bootstrap/cache || true
chmod -R u+rwX,g+rwX /srv/api/storage /srv/api/bootstrap/cache
# setgid-бит (2775) на каталоге логов: все новые файлы внутри автоматически наследуют группу каталога,
# что позволяет нескольким процессам (fpm-воркеры, php-воркеры) писать в один каталог без конфликтов прав.
chmod 2775 /srv/api/storage/logs

umask 0002

# Выбираем стратегию запуска в зависимости от команды:
# - php-fpm запускается от root, но воркеры понижаются до www-data через pool-конфиг — вмешиваться не нужно.
# - Все остальные команды (artisan, queue:work, cron и т.д.) запускаются от имени www-data через gosu,
#   чтобы создаваемые файлы принадлежали правильному пользователю.
if [ "$1" = "php-fpm" ] || [ "$1" = "php-fpm8.3" ]; then
  exec "$@"
else
  mkdir -p /var/log/worker
  chown -R "${WWW_UID}:${WWW_GID}" /var/log/worker || true
  chmod -R 775 /var/log/worker || true

  # gosu — аналог su/sudo, но корректно передаёт управление процессу (PID 1 = сам процесс, не shell-обёртка).
  # Если контейнер уже запущен не от root — gosu не нужен, запускаем напрямую.
  if [ "$(id -u)" = "0" ]; then
    exec gosu "${WWW_UID}:${WWW_GID}" "$@"
  else
    exec "$@"
  fi
fi
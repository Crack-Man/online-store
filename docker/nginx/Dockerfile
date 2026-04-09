FROM nginx:1.27-alpine

# gettext даёт нам envsubst — утилиту для подстановки переменных окружения в текстовые шаблоны.
# logrotate — ротация логов nginx внутри контейнера по расписанию (через cron или supervisor).
RUN apk add --no-cache gettext logrotate

# Шаблоны конфигов — entrypoint выберет нужный и подставит переменные окружения.
COPY conf.d/api.http.conf.template  /etc/nginx/templates/api.http.conf.template
COPY conf.d/api.https.conf.template /etc/nginx/templates/api.https.conf.template

# Правило ротации логов nginx
COPY logrotate/nginx /etc/logrotate.d/nginx

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Каталог для логов создаём явно — на случай если volume не примонтирован
RUN mkdir -p /var/log/nginx

EXPOSE 80 443 8080 8443

ENTRYPOINT ["entrypoint.sh"]
CMD ["nginx", "-g", "daemon off;"]

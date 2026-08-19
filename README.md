# online-store

## Установка

### Локальная среда
1. Скопировать env:
```bash
cp .env.local .env
cp api/.env.local api/.env
```
2. Скопировать docker-compose:
```bash
cp docker-compose.local.yml docker-compose.yml
```
3. Запустить контейнеры:
```bash
docker compose up -d
```
4. Установить зависимости:
```bash
docker compose exec php composer install
```
5. Выполнить миграции & сидеры:
```bash
docker compose exec php php artisan migrate --seed
```
6. API доступен по адресу `http://localhost:8080`

## Swagger UI
Документация API доступна по адресу `http://localhost:8080/swagger`

## Postman
Коллекция Postman доступна в файле `docs/Online-store.postman_collection.json`
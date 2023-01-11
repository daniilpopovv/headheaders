# HeadHeaders
HeadHeaders - аналог проекта HeadHunter.

## Требования:
* php > 8.1
* docker 

Наличение Symfony CLI:
```sh
scoop install symfony-cli
```

Scoop:
```sh
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
irm get.scoop.sh | iex
```

## Запуск сервера
1) Подъем докера c БД:
```sh
docker-compose up -d
```

2) Composer:
```sh
composer install
```

3) Npm:
```sh
npm install
```

4) Выполнение миграций:
```sh
symfony console doctrine:migrations:migrate
```

5) Добавить тестовые данные:
```sh
symfony run psql < dump.sql
```

6) Запуск сборщика frontend:
```sh
symfony run -d npm run watch
```

7) Запуск сервера:
```sh
symfony server:start -d
```

## Прочие команды сервера
Просмотр переменных окружения:
```sh
symfony var:export
```

## База данных
Взаимодействие через psql:
```sh
symfony run psql
```

Если команда psql не установлена:
```sh
docker-compose exec database psql app app
```

Резервное копирование:
```sh
symfony run pg_dump --data-only > dump.sql
```

Восстановление:
```sh
symfony run psql < dump.sql
```
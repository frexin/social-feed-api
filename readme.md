## Установка

`composer install`
`cp .env.example .env`

## Настройка

Настройки подключения в файле .env
```
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:voSbnuH8ULDk6WAb0+F+QdV+Qg180Xd2AUxuE79qQbI=
APP_URL=http://localhost

DB_HOST=127.0.0.1
DB_DATABASE={databaseName}
DB_USERNAME=root
DB_PASSWORD=root

CACHE_DRIVER=redis
SESSION_DRIVER=database
QUEUE_DRIVER=sync

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Выполнить

```
php artisan vendor:publish --provider="DCN\RBAC\RBACServiceProvider" --tag=config
php artisan vendor:publish --provider="DCN\RBAC\RBACServiceProvider" --tag=migrations
php artisan vendor:publish --provider='Frozennode\Administrator\AdministratorServiceProvider'
```

в консоли выполнить
`php artisan migrate`

Для заполнения тестовыми данными
`php artisan migrate --seed`

Сгенирировать новый APP_KEY в .env файле ПРИ RuntimeException: No supported encrypter found.
`php artisan key:generate`

## Парсинг данных

Парсинг всех источников
`./artisan parse:source`

Парсинг источника по id
`./artisan parse:source --id=1`

Парсинг источника по alias
`./artisan parse:source --alias=1`
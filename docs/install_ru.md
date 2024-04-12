# Установка

> :warning: СУБД Postgres является рекомендуемой к применению.

#### Системные требования

* PostgreSQL ^16
* php ^8.2

---

### 1. Создание чистой базы данных

```postgresql
CREATE DATABASE "task_management"
    ENCODING 'UTF8'
    LC_COLLATE = 'ru_RU.UTF-8'
    LC_CTYPE = 'ru_RU.UTF-8'
    TEMPLATE template0;
```
#### 1.1 Генерация пароля

```bash
bin/console security:hash-password
```

### 2. Установка `bundle`

#### 2.1 Установка конфигурации `services.yaml`

<details>
<summary>Нажмите, чтобы раскрыть/скрыть</summary>

```yaml
parameters:

services:
  _defaults:
    autowire: true
    autoconfigure: true
    
    cronvTaskManagement:
        resource: '@cronvTaskManagementBundle'
        exclude:
            - '@cronvTaskManagementBundle/{Entity, Repository, Interface, Resources, Security}/'
            - '@cronvTaskManagementBundle/cronvTaskManagementBundle.php'
```
</details>

#### 2.2 Установка маршрутизации

```yaml
cronv_tm:
  prefix: '/cronv/tm'
  resource: '@cronvTaskManagementBundle/Resources/config/routes.yaml'
```

#### 2.3 Установка `security.yaml`

<details>
<summary>Нажмите, чтобы раскрыть/скрыть</summary>

```yaml
security:
    enable_authenticator_manager: true

    # https://symfony.com/doc/current/security.html#registering-the-user-hashing-passwords
    password_hashers:
        cronv\Task\Management\Entity\User: 'auto'
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
    # https://symfony.com/doc/current/security.html#loading-the-user-the-user-provider
    providers:
        # users_in_memory: { memory: null }
        cronv_task_provider:
            entity:
                class: cronv\Task\Management\Entity\User
                property: username
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            lazy: true
            provider: cronv_task_provider
            custom_authenticator: cronv\Task\Management\Security\LoginFormAuth
            entry_point: cronv\Task\Management\Security\LoginFormAuth
            form_login:
                enable_csrf: true
                default_target_path: cronv-tm-bundle
                target_path_parameter: cronv-tm-bundle
                login_path: app_login
                check_path: app_login
                username_parameter: username
                password_parameter: password

            logout:
                path: app_logout
                target: app_login

            remember_me:
                secret: '%kernel.secret%'
                lifetime: 604800
                path: /
                always_remember_me: true
#            guard:
#              authenticators:
#                - cronv\Task\Management\Entity\User

            # activate different ways to authenticate
            # https://symfony.com/doc/current/security.html#the-firewall

            # https://symfony.com/doc/current/security/impersonating_user.html
            # switch_user: true

    # Easy way to control access for large sections of your site
    # Note: Only the *first* access control that matches will be used
    access_control:
        # - { path: ^/admin, roles: ROLE_ADMIN }
        # - { path: ^/profile, roles: ROLE_USER }
      - { path: ^/login, roles: ROLE_USER }
```
</details>

### 3. Настройка корневого файла настроек `.env`

```env
###> doctrine/doctrine-bundle ###
DATABASE_URL_PGSQL="postgresql://root:pass@db_pg:5432/task_management?serverVersion=16&charset=utf8"
###< doctrine/doctrine-bundle ###
```

### 3.1 Настройка `doctrine.yml`

<details>
<summary>Нажмите, чтобы раскрыть/скрыть</summary>

```yaml
doctrine:
    dbal:
        default_connection: default
        connections:
            default:
                url: '%env(resolve:DATABASE_URL_PGSQL)%'
    orm:
        default_entity_manager: default
        auto_generate_proxy_classes: true
        entity_managers:
            default:
                connection: default
                mappings:
                    cronvTaskManagementBundle:
                        is_bundle: true
                        dir: 'Entity'
                        prefix: 'cronv\Task\Management\Entity'
                        alias: TM
                dql:
                    string_functions:
                        ILIKE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ilike
```
</details>

### 4. Миграция

#### 4.1 Настройка миграции doctrine

Файл настроек миграции `config/packages/doctrine_migrations.yaml`

```yaml
doctrine_migrations:
    migrations_paths:
        'cronvTaskManagementMigrations': '@cronvTaskManagementBundle/migrations'
```

#### 4.2 Применение миграции
```bash
bin/console doctrine:migrations:migrate --em=default
```

#### 4.3 Откат миграции

`cronvTaskManagementMigrations\Version20231107121533` - Соответствует названию в столбце `version` таблицы миграции

```bash
bin/console doctrine:migrations:execute 'cronvTaskManagementMigrations\Version20231107121533' --down
```

### 5. Проверка часового пояса в глобальных настройках `php.ini`

:clock430: Измените настройки согласно вашему часовому поясу

```ini
[Date]
; Defines the default timezone used by the date functions
; https://php.net/date.timezone
date.timezone = Europe/Moscow
```

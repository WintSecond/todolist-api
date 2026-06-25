# TODOList API — Лаба 1 (Symfony REST + CRUD)

Каркас backend-проекта по требованиям лекции:
- REST-стиль (слайды 16-17)
- CRUD: Create=POST, Read=GET, Update=PUT, Delete=DELETE (слайд 18)
- Тема: TODOList (слайд 23)

## 1. Установка окружения

```bash
# PHP 8.1+, MySQL, Composer и Symfony CLI должны быть установлены заранее
php -v
composer -V
symfony version
```

## 2. Создание проекта

```bash
symfony new todolist-api
cd todolist-api

composer require symfony/serializer-pack
composer require symfony/security-bundle
composer require symfony/orm-pack
composer require --dev symfony/maker-bundle
```

## 3. Настройка базы данных

В файле `.env` укажите строку подключения:

```
DATABASE_URL="mysql://root:password@127.0.0.1:3306/todolist_db?serverVersion=8.0"
```

Создайте базу:

```bash
php bin/console doctrine:database:create
```

## 4. Сущность Todo

Скопируйте `src/Entity/Todo.php` и `src/Repository/TodoRepository.php` из этого
проекта в свой (или сгенерируйте репозиторий командой
`php bin/console make:entity` и допишите поля вручную — см. ниже).

Поля сущности:
- `id` (int, авто)
- `title` (string)
- `description` (text, nullable)
- `completed` (bool)
- `createdAt` (datetime, ставится автоматически при создании)

## 5. Миграция

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## 6. Контроллер

Скопируйте `src/Controller/Api/TodoController.php` — там уже реализованы
все CRUD-методы:

| Метод  | URL                  | Действие              |
|--------|----------------------|------------------------|
| GET    | /api/todos           | список всех задач      |
| GET    | /api/todos/{id}      | одна задача            |
| POST   | /api/todos           | создать задачу         |
| PUT    | /api/todos/{id}      | обновить задачу        |
| DELETE | /api/todos/{id}      | удалить задачу         |

## 7. Запуск

```bash
symfony server:start
# или
php -S 127.0.0.1:8000 -t public/
```

## 8. Проверка в Postman / curl

```bash
# создать задачу
curl -X POST http://127.0.0.1:8000/api/todos \
  -H "Content-Type: application/json" \
  -d '{"title":"Сделать лабу 1","description":"Symfony CRUD API"}'

# список задач
curl http://127.0.0.1:8000/api/todos

# обновить задачу id=1
curl -X PUT http://127.0.0.1:8000/api/todos/1 \
  -H "Content-Type: application/json" \
  -d '{"completed": true}'

# удалить задачу id=1
curl -X DELETE http://127.0.0.1:8000/api/todos/1
```
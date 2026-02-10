# DDD Database Integration Examples

Примеры различных подходов к интеграции предметной области (DDD) с базой данных.

## 📋 Описание

Данный репозиторий создан как практическая реализация концепций работы с БД, изложенных в книге:  
**[Domain-Driven Design in PHP](https://www.packtpub.com/product/domain-driven-design-in-php/9781788623069)**  
*Carlos Buenosvinos, Christian Soronellas и Keyvan Akbary*

Книга стала основой для выбора архитектурных решений в этом проекте, особенно в части:
- Разделения Domain и Infrastructure слоёв
- Обработки Value Objects
- Стратегий маппинга в реляционные БД

## 🌳 Структура веток

### `doctrine_orm_embeddables (main)`  - Doctrine Embeddables Approach

Реализация через Doctrine Embeddables для встраивания Value Objects напрямую в структуру сущностей.

- Чистая структура БД — поля VO становятся частью таблицы
- Автоматический маппинг — Doctrine сам управляет преобразованиями
- Поддержка вложенных полей без ручного преобразования типов
- Повторное использование — один VO можно использовать в разных сущностях
- Типобезопасность — ошибки на этапе компиляции
- Соответствие DDD — инфраструктурные детали отделены от домена

### `doctrine_orm_php_mapping` - Custom DBAL Types

Реализация через кастомные DBAL типы Doctrine ORM 3.6.
- Полный контроль над маппингом
- Чистая архитектура DDD
- Поддержка Value Objects
- Более сложная настройка, но лучшая масштабируемость

### `symfony_serializer` - Serializer Approach

Реализация через Symfony Serializer. Минимум кода, максимальная простота, но ограниченная гибкость.
- Быстрая реализация
- Меньше boilerplate кода
- Поддержка Value Objects
- Подходит для прототипов и небольших проектов

## 🛠️ Используемые технологии

### Основные зависимости
- **Symfony ORM Pack** - основной набор для работы с БД
- **Symfony Security Bundle** - аутентификация и авторизация
- **Symfony Serializer** - сериализация/десериализация объектов
- **Symfony Validator** - валидация данных
- **Symfony Password Hasher** - безопасное хеширование паролей

### Development зависимости
- **Symfony Maker Bundle** - генерация кода
- **Symfony Test Pack** - набор инструментов для тестирования
- **DAMA Doctrine Test Bundle** - тестирование с БД
- **Doctrine Fixtures Bundle** - тестовые данные

## 🚀 Поддерживаемые серверы запуска

Проект поддерживает несколько способов запуска PHP приложения через Docker:

### Доступные серверы для веток Main, doctrine_orm_embeddables:
- **PHP-FPM** - традиционный подход с Nginx
- **RoadRunner** - высокопроизводительный PHP application server
- **FrankenPHP** - современный сервер со встроенным PHP

### Настройка через .env
В корневом `.env` файле доступны настройки:

```env
# PHP-FPM
# Включен по умолчанию. Чтобы включить PHP-FPM закоментируйте нижние блоки

# Файлы roadrunner
PHP=php-rr
NGINX=nginx-off
NGINX_REVERSE_PROXY=nginx-reverse-proxy-default

# Файлы franken
#PHP=php-franken
#NGINX=nginx-off
#NGINX_REVERSE_PROXY=nginx-reverse-proxy-default
```
### Версии
- PHP 8.4
- Symfony 8

## 👤 Автор

GitHub: [b0030011](https://github.com/b0030011)
##

# Esia Mini

Проект на Docker для демонстрации интеграции с тестовым стендом ЕСИА при помощи PHP.

**Важно:** Потребуется мнемоника вашей информационной системы, зарегистрированной в ЕСИА, чтобы проект функционировал.

## Технологии

- Docker. Если на вашем ПК его нет, можете установить по [инструкции](https://docs.docker.com/engine/install/).
- PHP 8
- Nginx
- OpenSSL, с поддержкой ГОСТ (gost engine)

## Установка

1. Склонируем репозиторий: 
  ```
  git clone https://github.com/yesnik/esia-mini.git
  cd esia-mini
  ```
2. Убедимся, что Docker запущен на нашем ПК:
  ```
  service docker status
  ```
3. Установим зависимости:
  ```
  docker compose run php-cli composer install
  ```
4. Запустим Docker Compose
  ```
  docker compose up
  ```
5. Укажем мнемонику своей системы в параметре `clientId` в файлах `app/public/index.php`, `app/public/response.php`.

## Проверка работы

1. Открываем в браузере http://127.0.0.1:8000/
2. Нажимаем на ссылку "Войти через портал Госуслуги"
3. На тестовом портале вводим реквизиты тестовой учетной записи Госуслуг: EsiaTest002@yandex.ru / 11111111
4. Нас перенаправляет на страницу, где мы видим данные, полученные из Госуслуг.

## Настройка сертификатов

При попытке получить данные пользователя с портала ЕСИА может возникнуть ошибка:
```
Fatal error: Uncaught GuzzleHttp\Exception\RequestException: 
cURL error 60: SSL certificate problem: unable to get local issuer certificate 
(see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for https://esia-portal1.test.gosuslugi.ru/aas/oauth2/te 
```

**Важно:** Для устранения этой ошибки мы отключили проверку сертификатов, т.к. это самый простой способ. В файле `app/public/response.php` для этого указали:

```php
$client = new GuzzleHttpClient(
    new Client([
        'verify' => false,
    ])
);
```

Также ошибку можно устранить, импортировав сертификат тестового стенда ЕСИА в хранилище доверенных сертификатов в контейнере `php-fpm`:

1. Заходим в работающий контейнер php-fpm: `docker compose exec php-fpm sh`
2. Получаем информацию о сертификате сервера
    ```bash
    openssl s_client esia-portal1.test.gosuslugi.ru:443
    ```
    В выводе этой команды нужно найти сертификат, его примерный вид:
    ```
    -----BEGIN CERTIFICATE-----
    MIIHrTCCB1qgAwIBAgILAN/pVOMAAAAABiYwCgYIKoUDBwEBAwIwggE7MSEwHwYJ
    ...
    wzkYntwevFT0QnGIf6vUFRQJhadWgnX+OdPq4e3oq/2cNOi5eeYUDDpDHud1LOL/
    yg==
    -----END CERTIFICATE-----
    ```
3. Этот сертификат копируем в файл, к примеру `esia-test.crt`.
4. Помещаем файл в `/usr/local/share/ca-certificates`. Это папка для локальных CA сертификатов, которые должны иметь расширение `.crt`.
5. Выполняем команду для импорта сертификата: `update-ca-certificates`

### Полезная информация

* В [Методических рекомендациях](https://digital.gov.ru/ru/documents/6186/) говорится, что сертификаты тестовой и 
   продуктивной сред ЕСИА, используемые для формирования электронных подписей ответов как поставщика, доступны
   по ссылке: http://esia.gosuslugi.ru/public/esia.zip
* Скопируем сертификаты с сайта Госуслуг и распакуем:
  ```
  cd /usr/local/src
  wget --no-check-certificate https://esia.gosuslugi.ru/public/esia.zip
  unzip esia.zip
  ```
* Проверка корректности работы:
  ```
  openssl s_client -CApath /etc/ssl/certs -connect esia.gosuslugi.ru:443
  
  openssl s_client -CApath /etc/ssl/certs -connect esia-portal1.test.gosuslugi.ru:443
  ```
  В случае ошибки вы увидите: `Verify return code: 21 (unable to verify the first certificate)`, в случае успеха:
  `Verify return code: 0 (ok)`.

## Благодарности

- Александру Устименко - за проект [ekapusta/oauth2-esia](https://github.com/ekapusta/oauth2-esia), из которого 
  мы взяли сертификат, приватный ключ для работы с ЕСИА (см. папку `resources`).
- Станиславу Павловичеву - за проект [fr05t1k/esia](https://github.com/fr05t1k/esia), который мы используем для 
  взаимодействия с ЕСИА.

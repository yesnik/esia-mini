# Esia Mini

Проект для демонстрации работы с тестовым стендом ЕСИА.

**Важно:** У вас должна быть мнемоника вашей информационной системы, зарегистрированной в ЕСИА, чтобы проект функционировал.

## Технологии

- Docker
- PHP 8
- Nginx
- OpenSSL, с поддержкой ГОСТ (gost engine)

## Установка

1. Клонируем этот репозиторий: 
  ```
  git clone git@github.com:yesnik/esia-mini.git
  cd esia-mini
  ```
2. Устанавливаем зависимости:
  ```
  docker compose run php-cli composer install
  ```
3. Запускаем Docker Compose
  ```
  docker compose up
  ```
4. Указываем мнемонику своей системы в параметре `clientId` в файлах `public/index.php`, `public/response.php`.

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

**Важно:** Для устранения этой ошибки мы отключили проверку сертификатов, т.к. не нашли другого способа.

Полезная информация по этой теме:

* В контейнер нужно установить сертификаты сайта Госуслуг. Файл сертификата копируем в папку:
  ```
  /usr/local/share/ca-certificates
  ```
  Это папка для локальных CA сертификатов, которые должны иметь расширение `.crt`.
* Скопируем сертификаты с сайта Госуслуг и распакуем:
  ```
  cd /usr/local/src
  wget --no-check-certificate https://esia.gosuslugi.ru/public/esia.zip
  unzip esia.zip
  ```
* Скопируем сертификат для тестового стенда и запустим команду на его импорт:
  ```
  cp ssl_test_2022_ru.crt /usr/local/share/ca-certificates/
  update-ca-certificates
  c_rehash
  ```
* Скачиваем ГОСТ 34.10-2012 Корневой сертификат "Минкомсвязь России" от 02.07.2021 с официального сайта: https://ca.gisca.ru/support/repository/ 
  В нашем случае это: https://ca.gisca.ru/repository/AFF05C9E2464941E7EC2AB15C91539360B79AA9D.cer

* Конвертируем его в PEM-формат (расширение .crt): 
  ```
   openssl x509 -inform DER -in AFF05C9E2464941E7EC2AB15C91539360B79AA9D.cer -out minsvyaz.crt
  ```

* Проверяем корректность работы:
  ```
  openssl s_client -CApath /etc/ssl/certs -connect esia.gosuslugi.ru:443
  
  openssl s_client -CApath /etc/ssl/certs -connect esia-portal1.test.gosuslugi.ru:443
  ```
  В случае ошибки вы увидите: `Verify return code: 21 (unable to verify the first certificate)`, в случае успеха:
  `Verify return code: 0 (ok)`.
* В [Методических рекомендациях](https://digital.gov.ru/ru/documents/6186/) говорится, что сертификаты тестовой и 
   продуктивной сред ЕСИА, используемые для формирования электронных подписей ответов как поставщика, доступны
   по ссылке: http://esia.gosuslugi.ru/public/esia.zip

## Благодарности

- Александру Устименко - за проект [ekapusta/oauth2-esia](https://github.com/ekapusta/oauth2-esia), из которого 
  мы взяли сертификат, приватный ключ для работы с ЕСИА (см. папку `resources`).
- Станиславу Павловичеву - за проект [fr05t1k/esia](https://github.com/fr05t1k/esia), который мы используем для 
  взаимодействия с ЕСИА.
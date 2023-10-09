## Установка проекта
Требуется наличие Ubuntu с установленными на него composer, git, docker.

В терминале Ubuntu перейти в нужную директорию и запустить команды:
-     git clone https://github.com/Nickkolass/shop.git
-     composer install
-     cp .env.example .env
  - в файле .env в поле MAIL_USERNAME и MAIL_PASSWORD установить значения используемого почтового ящика в домене yandex.ru
  - в файле .env в поле TELEGRAM_LOGGER_CHAT_ID и TELEGRAM_LOGGER_BOT_TOKEN установить значения чата в telegram и токена приглашенного в него бота
  - в файле .env в поле YANDEX_DISK_OAUTH_TOKEN установить значение токена для сервиса Yandex.disk
  - при необходимости использования s3 хранилища YANDEX_CLOUD в файле .env в поля YANDEX_CLOUD_KEY, YANDEX_CLOUD_SECRET, YANDEX_CLOUD_BUCKET установить соответствующие значения сервиса 
- docker compose up -d
-     docker exec -it shop_app bash
  -     php artisan init

## Начало работы

Для просмотра функционала может быть использовано несколько вариантов:
- без регистрации: просмотр клиенской части приложения без возможности оформления заказов, добавления товаров в избранное, оставления комментариев.
- с регистрацией добавляются вышеуказанные возможности.
- под пользователем с правами клиента ['email' => '3@mail.ru', 'password' => '3'] добавляются тестовые данные: заказы, избранные товары, 
- под пользователем с правами продавца ['email' => '2@mail.ru', 'password' => '2'] добавляется возможность входа в админпанель с данными, относящимися к пользователю, 
- под пользователем с правами администратора ['email' => '1@mail.ru', 'password' => '1'] добавляется возможность просмтора админпанели, с данными всех пользователей.

При последующих запусках приложения в терминале выполнять команды
-     docker exec -it shop_app bash
  -     npm run dev

Для обработки процессов, поставленных в очередь выполнять команды
-     docker exec -it shop_app bash
  -     php artisan queue:work

Для запуска планировщика задач выполнять команды
-     docker exec -it shop_app bash
    -     php artisan schedule:work
  
Перед тестированием запустить
-     docker exec -it shop_app bash
  -     php artisan config:clear

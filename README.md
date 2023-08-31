## Установка проекта
Требуется наличие Ubuntu с установленными на него composer, git, docker.

В терминале Ubuntu перейти в нужную директорию и запустить команды:
-     git clone https://github.com/Nickkolass/shop.git
-     composer install
-     cp .env.example .env
- в файле .env в поле MAIL_USERNAME и MAIL_PASSWORD установить значения используемого почтового ящика в домене yandex.ru
- в файле .env в поле TELEGRAM_LOGGER_CHAT_ID и TELEGRAM_LOGGER_BOT_TOKEN установить значения чата в telegram и токена приглашенного в него бота
-     docker compose up -d
-     docker exec -it shop_app bash
  -     curl https://disk.yandex.ru/d/qzbLR1NnyshCBg/photo.zip -o storage/app/public/photo.zip
  -     unzip storage/app/public/photo -d storage/app/public
  -     rm storage/app/public/photo.zip
  -     php artisan init
  -     chmod 777 -R ./storage/app/public
  -     npm run dev

## Начало работы

Для просмотра функционала может быть использовано несколько вариантов:
- без регистрации: просмотр клиенской части приложения без возможности оформления заказов, добавления товаров в избранное, оставления комментариев.
- с регистрацией добавляются вышеуказанные возможности.
- под пользователем с правами клиента ['email' => '3@mail.ru', 'password' => '3'] добавляются тестовые данные: заказы, избранные товары, 
- под пользователем с правами продавца ['email' => '2@mail.ru', 'password' => '2'] добавляется возможность входа в админпанель с данными, относящимися к пользователю, 
- под пользователем с правами администратора ['email' => '1@mail.ru', 'password' => '1'] добавляется возможность просмтора админпанели, с данными всех пользователей.

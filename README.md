## Установка проекта
Требуется наличие Ubuntu с установленными на него composer, git, docker.

В терминале Ubuntu перейти в нужную директорию и запустить команды:
-     git clone https://github.com/Nickkolass/shop.git
-     composer install
-     cp .env.example .env
- в файле .env:
  -  в поле REDIS_HOST установить значение ip адреса wsl (для win10: параметры->сеть и интернет->просмотр свойств оборудования и сети->vEthernet (WSL)->IPv4)
  -  в поле MAIL_USERNAME и MAIL_PASSWORD установить значения используемого почтового ящика в домене yandex.ru
- поместить в ./storage/app/public изображения из https://disk.yandex.ru/d/qzbLR1NnyshCBg.
-     docker compose up -d
-     docker exec -it shop_app bash
  -     php artisan storage:link
  -     php artisan key:generate
  -     php artisan jwt:secret
  -     php artisan migrate --seed
  -     chmod 777 -R ./storage/app/public
  -     php artisan optimize
  -     npm run dev

## Начало работы

Для просмотра функционала может быть использовано несколько вариантов:
- без регистрации: просмотр клиенской части приложения без возможности оформления заказов, добавления товаров в избранное, оставления комментариев.
- с регистрацией добавляются вышеуказанные возможности.
- под пользователем с правами клиента ['email' => '3@mail.ru', 'password' => '3'] добавляются тестовые данные: заказы, избранные товары, 
- под пользователем с правами продавца ['email' => '2@mail.ru', 'password' => '2'] добавляется возможность входа в админпанель с данными, относящимися к пользователю, 
- под пользователем с правами администратора ['email' => '1@mail.ru', 'password' => '1'] добавляется возможность просмтора админпанели, с данными всех пользователей.

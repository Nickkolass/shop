## Установка проекта
Требуется наличие Ubuntu с установленными на него composer, git, node, docker.

В терминале Ubuntu перейти в нужную директорию и запустить команды:
-     git clone https://github.com/Nickkolass/shop.git <Директория>
- Перейти в <Директорию>
-     composer install
-     npm install
- дублировать файл .env.example и назвать его .env
- в файле .env:
  -  в поле BASE_URL установить значение ip адреса wsl с портом 8876 (для win10: параметры->сеть и интернет->просмотр свойств оборудования и сети->vEthernet (WSL)->IPv4)
  -  в поле MAIL_USERNAME и MAIL_PASSWORD установить значения используемого почтового ящика в домене yandex.ru
- поместить в директорию <Директория>/storage/app/public файлы из https://disk.yandex.ru/d/qzbLR1NnyshCBg.
-     docker compose up -d
-     docker exec -it shop_app bash
  -     php artisan storage:link
  -     php artisan key:generate
  -     php artisan jwt:secret
  -     php artisan migrate --seed
  -     exit
- В файле vendor/laravel/ui/auth-backend/RegistersUsers.php заменить:

      /**
      * Handle a registration request for the application.
      *
      * @param  \Illuminate\Http\Request  $request
      * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
      */
      public function register(Request $request)
      {
      $this->validator($request->all())->validate();
      
      event(new Registered($user = $this->create($request->all())));
  
    на

      use App\Mail\MailRegistered;
      use Illuminate\Auth\Events\Registered;
      use Illuminate\Support\Facades\Mail;
      use App\Http\Requests\Admin\User\UserStoreRequest;

      /**
      * Handle a registration request for the application.
      *
      * @param  UserStoreRequest  $request
      * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
      */
      public function register(UserStoreRequest $request)
      {
      $data = $request->validated();
      $user = $this->create($data);
      event(new Registered($user));
      Mail::to($user->email)->send(new MailRegistered());
-     sudo chmod 777 -R ./
-     npm run dev

## Начало работы

Для просмотра функционала может быть использовано несколько вариантов:
- без регистрации: просмотр клиенской части приложения без возможности оформления заказов, добавления товаров в избранное, оставления комментариев.
- с регистрацией добавляются вышеуказанные возможности.
- под пользователем с правами клиента ['email' => '3@mail.ru', 'password' => '3'] добавляются тестовые данные: заказы, избранные товары, 
- под пользователем с правами продавца ['email' => '2@mail.ru', 'password' => '2'] добавляется возможность входа в админпанель с данными, относящимися к пользователю, 
- под пользователем с правами администратора ['email' => '1@mail.ru', 'password' => '1'] добавляется возможность просмтора админпанели, с данными всех пользователей.

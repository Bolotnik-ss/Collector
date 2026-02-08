<?php
// Включаем сессию для отслеживания состояния пользователя
session_start();

// Подключаем необходимые файлы для работы с БД, моделями и сервисами
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/dbhelper.php';
require_once __DIR__ . '/../src/Service/HomeService.php';
require_once __DIR__ . '/../src/Controller/HomeController.php';
// Для работы с пользователями
require_once __DIR__ . '/../src/Model/User.php';
require_once __DIR__ . '/../src/Service/AuthService.php';
require_once __DIR__ . '/../src/Controller/AuthController.php';
// Для работы с монетами
require_once __DIR__ . '/../src/Model/CoinCollection.php';
require_once __DIR__ . '/../src/Model/Coin.php';
require_once __DIR__ . '/../src/Service/CoinCollectionService.php';
require_once __DIR__ . '/../src/Service/CoinService.php';
require_once __DIR__ . '/../src/Service/CoinDictionaryService.php';
require_once __DIR__ . '/../src/Controller/CoinCollectionController.php';
require_once __DIR__ . '/../src/Controller/CoinController.php';
require_once __DIR__ . '/../src/Controller/CoinDictionaryController.php';
// Для работы с банкнотами
require_once __DIR__ . '/../src/Model/BanknoteCollection.php';
require_once __DIR__ . '/../src/Model/Banknote.php';
require_once __DIR__ . '/../src/Service/BanknoteCollectionService.php';
require_once __DIR__ . '/../src/Service/BanknoteService.php';
require_once __DIR__ . '/../src/Service/BanknoteDictionaryService.php';
require_once __DIR__ . '/../src/Controller/BanknoteCollectionController.php';
require_once __DIR__ . '/../src/Controller/BanknoteController.php';
require_once __DIR__ . '/../src/Controller/BanknoteDictionaryController.php';
// Для работы с марками
require_once __DIR__ . '/../src/Model/StampCollection.php';
require_once __DIR__ . '/../src/Model/Stamp.php';
require_once __DIR__ . '/../src/Service/StampCollectionService.php';
require_once __DIR__ . '/../src/Service/StampService.php';
require_once __DIR__ . '/../src/Service/StampDictionaryService.php';
require_once __DIR__ . '/../src/Controller/StampCollectionController.php';
require_once __DIR__ . '/../src/Controller/StampController.php';
require_once __DIR__ . '/../src/Controller/StampDictionaryController.php';
// Для работы с открытками
require_once __DIR__ . '/../src/Model/PostcardCollection.php';
require_once __DIR__ . '/../src/Model/Postcard.php';
require_once __DIR__ . '/../src/Service/PostcardCollectionService.php';
require_once __DIR__ . '/../src/Service/PostcardService.php';
require_once __DIR__ . '/../src/Service/PostcardDictionaryService.php';
require_once __DIR__ . '/../src/Controller/PostcardCollectionController.php';
require_once __DIR__ . '/../src/Controller/PostcardController.php';
require_once __DIR__ . '/../src/Controller/PostcardDictionaryController.php';

// Получаем текущий URL-адреc
$request = $_SERVER['REQUEST_URI'];

// Убираем лишние слэши в конце пути
$request = rtrim($request, '/');

// Если запрос пустой, направляем на главную страницу
if ($request == '') {
    $request = '/';
}

// Настроим маршрутизацию
if ($request != '/login' && $request != '/register' && $request != '/' && 
    !preg_match('#^/verify-email/[a-f0-9]{64}$#', $request) && 
    !preg_match('#^/reset-password/[a-f0-9]{64}$#', $request) && 
    $request != '/reset_password' && 
    !isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
} elseif (($request == '/login' || $request == '/register') && isset($_SESSION['user_id'])) {
    header('Location: /');
    exit();
} elseif ($request == '/login') {
    // Создаем и вызываем контроллер для входа
    $controller = new AuthController();
    $controller->login(); // Не забываем, что в контроллере нужно добавить метод login()
    exit();
} elseif ($request == '/register') {
    // Создаем и вызываем контроллер для регистрации
    $controller = new AuthController();
    $controller->register(); // Не забываем, что в контроллере нужно добавить метод register()
    exit();
} elseif ($request == '/logout') {
    // Создаем и вызываем контроллер для выхода
    $controller = new AuthController();
    $controller->logout();
    exit();
} elseif ($request == '/profile') {
    // Маршрут для просмотра и редактирования профиля
    $controller = new AuthController();
    $controller->profile();
    exit();
} elseif ($request == '/profile/update') {
    // Маршрут для обновления профиля
    $controller = new AuthController();
    $controller->updateProfile();
    exit();
} elseif ($request == '/change_password') {
    // Маршрут для изменения пароля
    $controller = new AuthController();
    $controller->changePassword();
    exit();
} elseif ($request == '/change_email') {
    // Маршрут для изменения email
    $controller = new AuthController();
    $controller->changeEmail();
    exit();
} elseif ($request == '/delete_account') {
    // Маршрут для удаления аккаунта
    $controller = new AuthController();
    $controller->deleteAccount();
    exit();
} elseif ($request == '/reset_password') {
    // Маршрут для запроса сброса пароля
    $controller = new AuthController();
    $controller->resetPassword();
    exit();
} elseif ($request == '/') {
    // Главная страница
    $controller = new HomeController();
    $controller->index();
    exit();
} elseif ($request == '/coin_collections') {
    // Новый маршрут для просмотра всех коллекций
    $controller = new CoinCollectionController();
    $controller->index(); // Страница с коллекциями
    exit();
} elseif (preg_match('#^/coin_collection/(\d+)(?:\?.*)?$#', $request, $matches)) {
    // Маршрут вида /coin_collection/{id} с опциональными GET-параметрами
    $id = (int)$matches[1];
    $controller = new CoinCollectionController();
    $controller->show($id);
    exit();
} elseif (preg_match('#^/coin_collection/edit/(\d+)$#', $request, $matches)) {
    // Маршрут вида /coin_collection/edit/{id}
    $id = (int)$matches[1];
    $controller = new CoinCollectionController();
    $controller->edit($id);
    exit();
} elseif (preg_match('#^/coin_collection/delete/(\d+)$#', $request, $matches)) {
    // Маршрут вида /coin_collection/edit/{id}
    $id = (int)$matches[1];
    $controller = new CoinCollectionController();
    $controller->delete($id);
    exit();
} elseif ($request == '/coin_collection/create') {
    $controller = new CoinCollectionController();
    $controller->create(); // Страница создания коллекции
    exit();
} elseif (preg_match('#^/coin_collection/(\d+)/coin/create$#', $request, $matches)) {
    // Маршрут для создания монеты в коллекции /coin_collection/{collection_id}/coin/create
    $collection_id = (int)$matches[1];
    $controller = new CoinController();
    $controller->create($collection_id); // Страница создания монеты
    exit();
} elseif (preg_match('#^/coin_collection/(\d+)/coin/(\d+)/edit$#', $request, $matches)) {
    // Маршрут для редактирования монеты в коллекции /coin_collection/{collection_id}/coin/{coin_id}/edit
    $collection_id = (int)$matches[1];
    $coin_id = (int)$matches[2];
    $controller = new CoinController();
    $controller->edit($collection_id, $coin_id); // Страница редактирования монеты
    exit();
} elseif (preg_match('#^/coin_collection/(\d+)/coin/(\d+)/delete$#', $request, $matches)) {
    // Маршрут для удаления монеты из коллекции /coin_collection/{collection_id}/coin/{coin_id}/delete
    $collection_id = (int)$matches[1];
    $coin_id = (int)$matches[2];
    $controller = new CoinController();
    $controller->delete($collection_id, $coin_id); // Удаление монеты
    exit();
} elseif (preg_match('#^/coin_collection/(\d+)/coin/store$#', $request, $matches)) {
    $collection_id = (int)$matches[1];
    $controller = new CoinController();
    $controller->store($collection_id); // Обработать POST-запрос для добавления монеты
    exit();
} elseif (preg_match('#^/coin_collection/(\d+)/coin/(\d+)/update$#', $request, $matches)) {
    // Маршрут для обновления монеты
    $collection_id = (int)$matches[1];
    $coin_id = (int)$matches[2];
    $controller = new CoinController();
    $controller->update($coin_id); // Сохранение изменений монеты
    exit();
} elseif ($request == '/api/coins/dictionary/countries') {
    // API endpoint для добавления новой страны для монет
    $controller = new CoinDictionaryController();
    $controller->addCountry();
    exit();
} elseif ($request == '/api/coins/dictionary/types') {
    // API endpoint для добавления нового типа монеты
    $controller = new CoinDictionaryController();
    $controller->addType();
    exit();
} elseif ($request == '/api/coins/dictionary/units') {
    // API endpoint для добавления новой единицы измерения
    $controller = new CoinDictionaryController();
    $controller->addUnit();
    exit();
} elseif ($request == '/api/coins/dictionary/series') {
    // API endpoint для добавления новой серии монет
    $controller = new CoinDictionaryController();
    $controller->addSeries();
    exit();
} elseif ($request == '/api/coins/dictionary/rarities') {
    // API endpoint для добавления новой редкости монеты
    $controller = new CoinDictionaryController();
    $controller->addRarity();
    exit();
} elseif ($request == '/api/coins/dictionary/materials') {
    // API endpoint для добавления нового материала монеты
    $controller = new CoinDictionaryController();
    $controller->addMaterial();
    exit();
// Маршруты для банкнот
} elseif ($request == '/banknote_collections') {
    // Маршрут для просмотра всех коллекций банкнот
    $controller = new BanknoteCollectionController();
    $controller->index();
    exit();
} elseif (preg_match('#^/banknote_collection/(\d+)(?:\?.*)?$#', $request, $matches)) {
    // Маршрут вида /banknote_collection/{id} с опциональными GET-параметрами
    $id = (int)$matches[1];
    $controller = new BanknoteCollectionController();
    $controller->show($id);
    exit();
} elseif (preg_match('#^/banknote_collection/edit/(\d+)$#', $request, $matches)) {
    // Маршрут вида /banknote_collection/edit/{id}
    $id = (int)$matches[1];
    $controller = new BanknoteCollectionController();
    $controller->edit($id);
    exit();
} elseif (preg_match('#^/banknote_collection/delete/(\d+)$#', $request, $matches)) {
    // Маршрут вида /banknote_collection/delete/{id}
    $id = (int)$matches[1];
    $controller = new BanknoteCollectionController();
    $controller->delete($id);
    exit();
} elseif ($request == '/banknote_collection/create') {
    $controller = new BanknoteCollectionController();
    $controller->create(); // Страница создания коллекции
    exit();
} elseif (preg_match('#^/banknote_collection/(\d+)/banknote/create$#', $request, $matches)) {
    // Маршрут для создания банкноты в коллекции /banknote_collection/{collection_id}/banknote/create
    $collection_id = (int)$matches[1];
    $controller = new BanknoteController();
    $controller->create($collection_id); // Страница создания банкноты
    exit();
} elseif (preg_match('#^/banknote_collection/(\d+)/banknote/(\d+)/edit$#', $request, $matches)) {
    // Маршрут для редактирования банкноты в коллекции /banknote_collection/{collection_id}/banknote/{banknote_id}/edit
    $collection_id = (int)$matches[1];
    $banknote_id = (int)$matches[2];
    $controller = new BanknoteController();
    $controller->edit($collection_id, $banknote_id); // Страница редактирования банкноты
    exit();
} elseif (preg_match('#^/banknote_collection/(\d+)/banknote/(\d+)/delete$#', $request, $matches)) {
    // Маршрут для удаления банкноты из коллекции /banknote_collection/{collection_id}/banknote/{banknote_id}/delete
    $collection_id = (int)$matches[1];
    $banknote_id = (int)$matches[2];
    $controller = new BanknoteController();
    $controller->delete($collection_id, $banknote_id); // Удаление банкноты
    exit();
} elseif (preg_match('#^/banknote_collection/(\d+)/banknote/store$#', $request, $matches)) {
    $collection_id = (int)$matches[1];
    $controller = new BanknoteController();
    $controller->store($collection_id); // Обработать POST-запрос для добавления банкноты
    exit();
} elseif (preg_match('#^/banknote_collection/(\d+)/banknote/(\d+)/update$#', $request, $matches)) {
    // Маршрут для обновления банкноты
    $collection_id = (int)$matches[1];
    $banknote_id = (int)$matches[2];
    $controller = new BanknoteController();
    $controller->update($collection_id, $banknote_id); // Передаем оба параметра
    exit();
} elseif ($request == '/api/banknotes/dictionary/countries') {
    // API endpoint для добавления новой страны для банкнот
    $controller = new BanknoteDictionaryController();
    $controller->addCountry();
    exit();
} elseif ($request == '/api/banknotes/dictionary/types') {
    // API endpoint для добавления нового типа банкноты
    $controller = new BanknoteDictionaryController();
    $controller->addType();
    exit();
} elseif ($request == '/api/banknotes/dictionary/units') {
    // API endpoint для добавления новой единицы измерения
    $controller = new BanknoteDictionaryController();
    $controller->addUnit();
    exit();
} elseif ($request == '/api/banknotes/dictionary/series') {
    // API endpoint для добавления новой серии банкнот
    $controller = new BanknoteDictionaryController();
    $controller->addSeries();
    exit();
} elseif ($request == '/api/banknotes/dictionary/rarities') {
    // API endpoint для добавления новой редкости банкноты
    $controller = new BanknoteDictionaryController();
    $controller->addRarity();
    exit();
} elseif ($request == '/api/banknotes/dictionary/materials') {
    // API endpoint для добавления нового материала банкноты
    $controller = new BanknoteDictionaryController();
    $controller->addMaterial();
    exit();
} elseif ($request == '/stamp_collections') {
    // Маршрут для просмотра всех коллекций марок
    $controller = new StampCollectionController();
    $controller->index();
    exit();
} elseif (preg_match('#^/stamp_collection/(\d+)(?:\?.*)?$#', $request, $matches)) {
    // Маршрут вида /stamp_collection/{id} с опциональными GET-параметрами
    $id = (int)$matches[1];
    $controller = new StampCollectionController();
    $controller->show($id);
    exit();
} elseif (preg_match('#^/stamp_collection/edit/(\d+)$#', $request, $matches)) {
    // Маршрут вида /stamp_collection/edit/{id}
    $id = (int)$matches[1];
    $controller = new StampCollectionController();
    $controller->edit($id);
    exit();
} elseif (preg_match('#^/stamp_collection/delete/(\d+)$#', $request, $matches)) {
    // Маршрут вида /stamp_collection/delete/{id}
    $id = (int)$matches[1];
    $controller = new StampCollectionController();
    $controller->delete($id);
    exit();
} elseif ($request == '/stamp_collection/create') {
    $controller = new StampCollectionController();
    $controller->create();
    exit();
} elseif (preg_match('#^/stamp_collection/(\d+)/stamp/create$#', $request, $matches)) {
    // Маршрут для создания марки в коллекции
    $collection_id = (int)$matches[1];
    $controller = new StampController();
    $controller->create($collection_id);
    exit();
} elseif (preg_match('#^/stamp_collection/(\d+)/stamp/(\d+)/edit$#', $request, $matches)) {
    // Маршрут для редактирования марки в коллекции
    $collection_id = (int)$matches[1];
    $stamp_id = (int)$matches[2];
    $controller = new StampController();
    $controller->edit($collection_id, $stamp_id);
    exit();
} elseif (preg_match('#^/stamp_collection/(\d+)/stamp/(\d+)/delete$#', $request, $matches)) {
    // Маршрут для удаления марки из коллекции
    $collection_id = (int)$matches[1];
    $stamp_id = (int)$matches[2];
    $controller = new StampController();
    $controller->delete($collection_id, $stamp_id);
    exit();
} elseif (preg_match('#^/stamp_collection/(\d+)/stamp/store$#', $request, $matches)) {
    $collection_id = (int)$matches[1];
    $controller = new StampController();
    $controller->store($collection_id);
    exit();
} elseif (preg_match('#^/stamp_collection/(\d+)/stamp/(\d+)/update$#', $request, $matches)) {
    // Маршрут для обновления марки
    $collection_id = (int)$matches[1];
    $stamp_id = (int)$matches[2];
    $controller = new StampController();
    $controller->update($collection_id, $stamp_id);
    exit();
} elseif ($request == '/api/stamps/dictionary/countries') {
    // API endpoint для добавления новой страны для марок
    $controller = new StampDictionaryController();
    $controller->addCountry();
    exit();
} elseif ($request == '/api/stamps/dictionary/units') {
    // API endpoint для добавления новой единицы измерения для марок
    $controller = new StampDictionaryController();
    $controller->addUnit();
    exit();
} elseif ($request == '/api/stamps/dictionary/types') {
    // API endpoint для добавления нового типа марки
    $controller = new StampDictionaryController();
    $controller->addType();
    exit();
} elseif ($request == '/api/stamps/dictionary/series') {
    // API endpoint для добавления новой серии марок
    $controller = new StampDictionaryController();
    $controller->addSeries();
    exit();
} elseif ($request == '/api/stamps/dictionary/rarities') {
    // API endpoint для добавления новой редкости марки
    $controller = new StampDictionaryController();
    $controller->addRarity();
    exit();
} elseif ($request == '/api/stamps/dictionary/materials') {
    // API endpoint для добавления нового материала марки
    $controller = new StampDictionaryController();
    $controller->addMaterial();
    exit();
} elseif ($request == '/api/stamps/dictionary/themes') {
    // API endpoint для добавления новой темы марки
    $controller = new StampDictionaryController();
    $controller->addTheme();
    exit();
} elseif ($request == '/postcard_collections') {
    // Маршрут для просмотра всех коллекций открыток
    $controller = new PostcardCollectionController();
    $controller->index();
    exit();
} elseif (preg_match('#^/postcard_collection/(\d+)(?:\?.*)?$#', $request, $matches)) {
    // Маршрут вида /postcard_collection/{id} с опциональными GET-параметрами
    $id = (int)$matches[1];
    $controller = new PostcardCollectionController();
    $controller->show($id);
    exit();
} elseif (preg_match('#^/postcard_collection/edit/(\d+)$#', $request, $matches)) {
    // Маршрут вида /postcard_collection/edit/{id}
    $id = (int)$matches[1];
    $controller = new PostcardCollectionController();
    $controller->edit($id);
    exit();
} elseif (preg_match('#^/postcard_collection/delete/(\d+)$#', $request, $matches)) {
    // Маршрут вида /postcard_collection/delete/{id}
    $id = (int)$matches[1];
    $controller = new PostcardCollectionController();
    $controller->delete($id);
    exit();
} elseif ($request == '/postcard_collection/create') {
    $controller = new PostcardCollectionController();
    $controller->create();
    exit();
} elseif (preg_match('#^/postcard_collection/(\d+)/postcard/create$#', $request, $matches)) {
    // Маршрут для создания открытки в коллекции
    $collection_id = (int)$matches[1];
    $controller = new PostcardController();
    $controller->create($collection_id);
    exit();
} elseif (preg_match('#^/postcard_collection/(\d+)/postcard/(\d+)/edit$#', $request, $matches)) {
    // Маршрут для редактирования открытки в коллекции
    $collection_id = (int)$matches[1];
    $postcard_id = (int)$matches[2];
    $controller = new PostcardController();
    $controller->edit($collection_id, $postcard_id);
    exit();
} elseif (preg_match('#^/postcard_collection/(\d+)/postcard/(\d+)/delete$#', $request, $matches)) {
    // Маршрут для удаления открытки из коллекции
    $collection_id = (int)$matches[1];
    $postcard_id = (int)$matches[2];
    $controller = new PostcardController();
    $controller->delete($collection_id, $postcard_id);
    exit();
} elseif (preg_match('#^/postcard_collection/(\d+)/postcard/store$#', $request, $matches)) {
    $collection_id = (int)$matches[1];
    $controller = new PostcardController();
    $controller->store($collection_id);
    exit();
} elseif (preg_match('#^/postcard_collection/(\d+)/postcard/(\d+)/update$#', $request, $matches)) {
    // Маршрут для обновления открытки
    $collection_id = (int)$matches[1];
    $postcard_id = (int)$matches[2];
    $controller = new PostcardController();
    $controller->update($collection_id, $postcard_id);
    exit();
} elseif ($request == '/api/postcards/dictionary/countries') {
    // API endpoint для добавления новой страны для открыток
    $controller = new PostcardDictionaryController();
    $controller->addCountry();
    exit();
} elseif ($request == '/api/postcards/dictionary/types') {
    // API endpoint для добавления нового типа открытки
    $controller = new PostcardDictionaryController();
    $controller->addType();
    exit();
} elseif ($request == '/api/postcards/dictionary/series') {
    // API endpoint для добавления новой серии открыток
    $controller = new PostcardDictionaryController();
    $controller->addSeries();
    exit();
} elseif ($request == '/api/postcards/dictionary/rarities') {
    // API endpoint для добавления новой редкости открытки
    $controller = new PostcardDictionaryController();
    $controller->addRarity();
    exit();
} elseif ($request == '/api/postcards/dictionary/materials') {
    // API endpoint для добавления нового материала открытки
    $controller = new PostcardDictionaryController();
    $controller->addMaterial();
    exit();
} elseif ($request == '/api/postcards/dictionary/themes') {
    // API endpoint для добавления новой темы открытки
    $controller = new PostcardDictionaryController();
    $controller->addTheme();
    exit();
} elseif ($request == '/api/postcards/dictionary/publishers') {
    // API endpoint для добавления нового издателя открытки
    $controller = new PostcardDictionaryController();
    $controller->addPublisher();
    exit();
} elseif (preg_match('#^/verify-email/([a-f0-9]{64})$#', $request, $matches)) {
    // Маршрут для подтверждения email
    $controller = new AuthController();
    $controller->verifyEmail($matches[1]);
    exit();
} elseif (preg_match('#^/reset-password/([a-f0-9]{64})$#', $request, $matches)) {
    // Маршрут для сброса пароля
    $controller = new AuthController();
    $controller->resetPasswordConfirm($matches[1]);
    exit();
} elseif (preg_match('#^/confirm-email-change/([a-f0-9]{64})$#', $request, $matches)) {
    // Маршрут для подтверждения смены email
    $controller = new AuthController();
    $controller->confirmEmailChange($matches[1]);
    exit();
} else {
    // Страница не найдена
    http_response_code(404);
    echo "404 Страница не найдена";
}

<?php

class HomeController {

    public function index() {
        // Получение данных о коллекциях
        $homeService = new HomeService();
        $coin_collections_count = $homeService->getCoinCollectionsCount();
        $banknote_collections_count = $homeService->getBanknoteCollectionsCount();
        $stamp_collections_count = $homeService->getStampCollectionsCount();
        $postcard_collections_count = $homeService->getPostcardCollectionsCount();

        // Передача данных в представление
        require __DIR__ . '/../View/index.php';
    }
} 
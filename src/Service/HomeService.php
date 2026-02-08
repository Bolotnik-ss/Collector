<?php

class HomeService {

    public function getCoinCollectionsCount() {
        $coinCollectionService = new CoinCollectionService();
        return $coinCollectionService->getCollectionsCount();
    }

    public function getBanknoteCollectionsCount() {
        $banknoteCollectionService = new BanknoteCollectionService();
        return $banknoteCollectionService->getCollectionsCount();
    }

    public function getStampCollectionsCount() {
        $stampCollectionService = new StampCollectionService();
        return $stampCollectionService->getCollectionsCount();
    }

    public function getPostcardCollectionsCount() {
        $postcardCollectionService = new PostcardCollectionService();
        return $postcardCollectionService->getCollectionsCount();
    }
} 
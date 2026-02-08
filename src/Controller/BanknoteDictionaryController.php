<?php

class BanknoteDictionaryController {
    private $dictionaryService;

    public function __construct() {
        $this->dictionaryService = new BanknoteDictionaryService();
    }

    private function handleAddRequest($method) {
        // Проверяем, что запрос пришел методом POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
            return;
        }

        // Получаем данные из тела запроса
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || empty(trim($data['name']))) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Название обязательно для заполнения']);
            return;
        }

        try {
            // Добавляем новую запись
            $id = $method($data['name']);
            
            // Возвращаем успешный ответ
            echo json_encode([
                'success' => true,
                'id' => $id,
                'message' => 'Запись успешно добавлена'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function addCountry() {
        $this->handleAddRequest([$this->dictionaryService, 'addCountry']);
    }

    public function addType() {
        $this->handleAddRequest([$this->dictionaryService, 'addType']);
    }

    public function addUnit() {
        $this->handleAddRequest([$this->dictionaryService, 'addUnit']);
    }

    public function addSeries() {
        $this->handleAddRequest([$this->dictionaryService, 'addSeries']);
    }

    public function addRarity() {
        $this->handleAddRequest([$this->dictionaryService, 'addRarity']);
    }

    public function addMaterial() {
        $this->handleAddRequest([$this->dictionaryService, 'addMaterial']);
    }
} 
<?php
namespace App\Services;

use App\Models\Category;

class ClassifierService {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new Category();
    }

    public function classify($subject, $body) {
        $textToAnalyze = strtolower($subject . " " . $body);
        $categories = $this->categoryModel->getWithKeywords();

        foreach ($categories as $category) {
            $keywords = json_decode($category['palabras_clave'], true);
            if (is_array($keywords)) {
                foreach ($keywords as $keyword) {
                    if (strpos($textToAnalyze, strtolower($keyword)) !== false) {
                        return [
                            'id_categoria' => $category['id_categoria'],
                            'id_area' => $category['id_area_responsable']
                        ];
                    }
                }
            }
        }
        
        // Si no hay coincidencias
        return null;
    }
}

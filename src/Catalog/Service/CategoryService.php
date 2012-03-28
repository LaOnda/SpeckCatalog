<?php

namespace Catalog\Service;

class CategoryService extends ServiceAbstract
{
    protected $productService;

    public function populateModel($category)
    {
        $categories = $this->getModelMapper()->getChildCategories($category->getCategoryId());
        if($categories){
            foreach($categories as $i => $childCategory){
                $childCategories[$i] = $this->populateModel($childCategory);
                $category->setCategories($childCategories);
            }
        }
        $products = $this->getProductService()->getProductsByCategoryId($category->getCategoryId());
        if($products){
            $category->setProducts($products);
        }
        return $category;   
    }

    public function getChildCategories($categoryId)
    {
        $categories = $this->getModelMapper()->getChildCategories($categoryId);
        foreach ($categories as $i => $category){
            $categories[$i] = $this->populateModel($category);
        }
        return $categories;
    } 

    public function getAll()
    {
        $categories = $this->getModelMapper()->getAll();
        foreach ($categories as $i => $category){
            $categories[$i] = $this->populateModel($category);
        }
        return $categories;
    }

    public function newCategoryCategory($parentCategoryId)
    {
        $category = $this->newModel();
        $this->linkParentCategory($parentCategoryId, $category->getCategoryId());
        
        return $category;  
    }

    public function linkParentCategory($parentId, $childId)
    {
        return $this->getModelMapper()->linkParentCategory($parentId, $childId);
    }

    public function getProductService()
    {
        return $this->productService;
    }

    public function setProductService($productService)
    {
        $this->productService = $productService;
        return $this;
    }
}

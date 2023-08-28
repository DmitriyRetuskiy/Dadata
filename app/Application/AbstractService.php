<?php

namespace Application;

use Infrastructure\Persistance\Memory\Product as ProductRepository;

class AbstractService
{
    public ProductRepository $productRepository;

    public function __construct()
    {
        $this->productRepository = ProductRepository::make();
    }
}

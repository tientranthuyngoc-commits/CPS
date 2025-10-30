<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Brand;

class BrandController
{
    public function index(): void
    {
        $brandId = (int)($_GET['id'] ?? 0);
        $sort = $_GET['sort'] ?? 'newest';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = max(6, (int)($_GET['limit'] ?? 12));
        $offset = ($page-1)*$limit;
        $res = Product::fetch([
            'brand_id' => $brandId,
            'order' => $sort,
            'limit' => $limit,
            'offset' => $offset,
        ]);
        $products = $res['items'];
        $total = (int)$res['total'];
        $pages = (int)ceil($total/$limit);
        $brand = $brandId ? Brand::find($brandId) : null;
        $paging = ['total'=>$total,'pages'=>$pages,'page'=>$page,'limit'=>$limit,'sort'=>$sort];
        require __DIR__.'/../../views/brand.php';
    }
}


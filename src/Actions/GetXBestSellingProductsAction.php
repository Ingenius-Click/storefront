<?php

namespace Ingenius\Storefront\Actions;

use Ingenius\Core\Services\PackageHookManager;

class GetXBestSellingProductsAction {

    public function __construct(
        protected PackageHookManager $hookManager
    ) {
        //
    }

    public function handle(int $limit = 10) {
        // Implementation to get the top X best-selling products
        $productClass = config('storefront.product_model');

        $query = $productClass::query();

        $query = $this->hookManager->execute('products.query.best_selling', $query, []);

        return $query->limit($limit)->get();
    }
    
}
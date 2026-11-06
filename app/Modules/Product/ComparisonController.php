<?php
namespace App\Modules\Product;

use App\Core\Controller;

/** Read-only product comparison. Uses existing catalog models and public visibility rules. */
class ComparisonController extends Controller {
    public function products(): void {
        $raw = $_GET['ids'] ?? '';
        if (!is_string($raw) || !preg_match('/^[1-9][0-9]{0,9}(,[1-9][0-9]{0,9}){0,3}$/D', $raw)) {
            $this->json(['message' => 'Vui lòng chọn từ 1 đến 4 PC hợp lệ.'], 422);
            return;
        }
        $ids = array_values(array_unique(array_map('intval', explode(',', $raw))));
        $model = new ProductModel();
        $products = [];
        foreach ($ids as $id) {
            $product = $model->find($id);
            if (!$product || (int)$product['status'] !== 1 || (int)$product['category_id'] < 1 || (int)$product['category_id'] > 3) continue;
            $price = (float)$product['price'];
            $discount = (float)($product['discount_price'] ?? 0);
            $products[] = [
                'id' => (int)$product['id'], 'name' => $product['name'],
                'category' => (int)$product['category_id'], 'quantity' => (int)$product['quantity'],
                'price' => $discount > 0 && $discount < $price ? $discount : $price,
                'image' => $product['thumbnail'], 'url' => base_url('san-pham/' . rawurlencode($product['slug'])),
                'specs' => json_decode($product['specifications'] ?? '{}', true) ?: [],
            ];
        }
        header('Cache-Control: no-store');
        $this->json(['products' => $products]);
    }
}

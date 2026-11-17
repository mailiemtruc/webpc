<?php
namespace App\Modules\PCBuilder;

use App\Core\Controller;
use App\Core\Middleware;

class PCBuilderController extends Controller
{
    private PCBuilderModel $model;
    public function __construct() { $this->model = new PCBuilderModel(); }

    public function index(): void
    {
        $this->view('client.pc_builder.index', [
            'title' => 'Xây dựng cấu hình PC — PCMarket',
            'components' => $this->model->components(),
        ]);
    }

    public function getComponents(): void
    {
        $this->json(['success' => true, 'components' => $this->model->components()]);
    }

    public function addAllToCart(): void
    {
        Middleware::csrf();
        $raw = json_decode((string)($_POST['product_ids'] ?? '[]'), true);
        if (!is_array($raw)) { $this->json(['success'=>false,'message'=>'Danh sách linh kiện không hợp lệ.'], 422); return; }
        $ids = array_values(array_unique(array_filter(array_map('intval', $raw), fn($id) => $id > 0)));
        if (!$ids) { $this->json(['success'=>false,'message'=>'Hãy chọn ít nhất một linh kiện trước khi thêm vào giỏ.'], 422); return; }

        $products = $this->model->findAvailableProducts($ids);
        if (count($products) !== count($ids)) { $this->json(['success'=>false,'message'=>'Một hoặc nhiều linh kiện không còn kinh doanh. Vui lòng tải lại cấu hình.'], 422); return; }
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) $_SESSION['cart'] = [];

        foreach ($products as $id => $p) {
            $existing = (int)($_SESSION['cart'][$id]['quantity'] ?? 0);
            if ((int)$p['quantity'] < $existing + 1) {
                $this->json(['success'=>false,'message'=>'Linh kiện “'.$p['name'].'” không đủ tồn kho.'], 422);
                return;
            }
        }
        foreach ($products as $id => $p) {
            $oldQty = (int)($_SESSION['cart'][$id]['quantity'] ?? 0);
            $_SESSION['cart'][$id] = [
                'id'=>(int)$p['id'], 'name'=>$p['name'], 'slug'=>'', 'sku'=>$p['sku'],
                'price'=>(float)$p['current_price'], 'original_price'=>(float)$p['price'],
                'thumbnail'=>$p['thumbnail'], 'quantity'=>$oldQty + 1,
            ];
        }
        $count = array_sum(array_map(fn($item)=>(int)$item['quantity'], $_SESSION['cart']));
        $this->json(['success'=>true,'message'=>'Đã thêm cấu hình vào giỏ hàng.','cartCount'=>$count,'redirect'=>base_url('gio-hang')]);
    }
}

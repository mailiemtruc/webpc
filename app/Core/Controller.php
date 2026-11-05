<?php
namespace App\Core;

abstract class Controller {
    /**
     * Render một view file
     */
    protected function view(string $viewPath, array $data = []): void {
        view($viewPath, $data);
    }

    /**
     * Trả về dữ liệu dạng JSON cho Ajax
     */
    protected function json(array $data, int $status = 200): void {
        json_response($data, $status);
    }

    /**
     * Chuyển hướng trang
     */
    protected function redirect(string $path): void {
        redirect($path);
    }

    /**
     * Chuyển hướng kèm flash message thông báo
     */
    protected function redirectWith(string $path, string $type, string $message): void {
        set_flash($type, $message);
        redirect($path);
    }
}


<?php
use App\Core\Router;

Router::get('/api/compare', 'App\Modules\Product\ComparisonController@products');

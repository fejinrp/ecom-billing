<?php

$dir = __DIR__ . '/database/migrations/';
$files = scandir($dir);

$pkMapping = [
    'ausers' => 'user_id',
    'brands' => 'brand_id',
    'category' => 'cat_id',
    'eorders' => 'order_id',
    'eorder_item' => 'item_id',
    'expdetails' => 'exp_id',
    'expname' => 'exp_id',
    'kkpincode' => 'pinid',
    'orderbal' => 'order_idbal',
    'orders' => 'order_id',
    'ordertrackhistory' => 'id',
    'order_item' => 'item_id',
    'proddetails' => 'product_id',
    'productreviews' => 'id',
    'products' => 'id',
    'purbal' => 'bal_id',
    'p_item' => 'pitem_id',
    'p_orders' => 'porder_id',
    'p_price' => 'price_id',
    'qorders' => 'order_id',
    'qorder_item' => 'item_id',
    'retproduct' => 'ret_id',
    'sorderbal' => 'order_idbal',
    'sorders' => 'order_id',
    'sorder_item' => 'item_id',
    'subcategory' => 'id',
    'tempt' => 'tid',
    'uorder' => 'orderid',
    'uorderbal' => 'balid',
    'uorders' => 'id',
    'usercheck' => 'id',
    'userlog' => 'id',
    'users' => 'id',
    'wishlist' => 'id'
];

foreach ($files as $file) {
    if ($file === '.' || $file === '..' || !str_ends_with($file, '.php')) continue;
    
    $filePath = $dir . $file;
    $content = file_get_contents($filePath);
    
    // Find table name in Schema::create('table_name',
    if (preg_match("/Schema::create\('([^']+)',/i", $content, $matches)) {
        $tableName = $matches[1];
        if (isset($pkMapping[$tableName])) {
            $pkName = $pkMapping[$tableName];
            
            // We want to find $table->integer('pkName') or $table->integer('pkName')->nullable()
            // and replace it with $table->id('pkName')
            $target = "\$table->integer('{$pkName}')";
            $replacement = "\$table->id('{$pkName}')";
            
            if (str_contains($content, $target)) {
                $content = str_replace($target, $replacement, $content);
                file_put_contents($filePath, $content);
                echo "Fixed Primary Key for table [{$tableName}] (column: {$pkName}) in {$file}\n";
            }
        }
    }
}
echo "All primary keys updated successfully!\n";

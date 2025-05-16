<?php

header('Content-Type: application/json');

if (isset($_GET['category'])) {
    $xml = simplexml_load_file('../data/brands.xml');
    $brands = [];
    
    foreach ($xml->category as $cat) {
        if ((string)$cat['name'] === $_GET['category']) {
            foreach ($cat->brand as $brand) {
                $brands[] = (string)$brand;
            }
            break;
        }
    }
    
    echo json_encode($brands);
}
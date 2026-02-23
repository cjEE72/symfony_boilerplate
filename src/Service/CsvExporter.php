<?php
namespace App\Service;

use App\Entity\Product;

class CsvExporter
{
    public function exportProductsToCsv(array $products): string
    {
        $handle = fopen('php://temp', 'r+');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($handle, ['id','name','description','price','type','weight','height','width','downloadUrl','licenseKey'], ';', '"', "\\");

        foreach ($products as $product) {
            if (!$product instanceof Product) {
                continue;
            }

            $row = [
                $product->getId(),
                $product->getName() ?? '',
                $product->getDescription() ?? '',
                $product->getPrice() ?? '',
                $product->getType() ?? '',
                $product->getWeight() ?? '',
                $product->getHeight() ?? '',
                $product->getWidth() ?? '',
                $product->getDownloadUrl() ?? '',
                $product->getLicenseKey() ?? ''
            ];

            fputcsv($handle, $row, ';', '"', "\\");
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }
}
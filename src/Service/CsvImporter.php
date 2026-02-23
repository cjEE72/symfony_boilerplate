<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class CsvImporter
{
    public function importFromPath(string $path, EntityManagerInterface $em, ProductRepository $productRepository): array
    {
        $result = ['created' => 0, 'updated' => 0, 'errors' => []];

        $csv = $this->readCsv($path);
        if ($csv === null) {
            $result['errors'][] = 'Unable to open file';
            return $result;
        }

        [$header, $rows] = $csv;

        if ($header === null) {
            $result['errors'][] = 'Missing header';
            return $result;
        }

        foreach ($rows as $row) {
            if (count($row) === 1 && $row[0] === null) {
                continue;
            }

            $data = $this->mapRowToData($header, $row);
            $id = isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null;
            $product = $id ? $productRepository->find($id) : null;

            $isNew = $product === null;
            if ($isNew) {
                $product = new Product();
            }

            $this->applyDataToProduct($product, $data);

            if ($isNew) {
                $em->persist($product);
                $result['created']++;
            } else {
                $result['updated']++;
            }
        }

        $em->flush();

        return $result;
    }

    private function readCsv(string $path): ?array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return null;
        }

        $rows = [];
        while (($row = fgetcsv($handle, 0, ';', '"', "\\")) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        if (count($rows) === 0) {
            return [null, []];
        }

        $header = array_map('trim', $rows[0]);
        $dataRows = array_slice($rows, 1);
        return [$header, $dataRows];
    }

    private function mapRowToData(array $header, array $row): array
    {
        $data = [];
        foreach ($header as $i => $col) {
            $data[$col] = isset($row[$i]) ? trim($row[$i]) : null;
        }
        return $data;
    }

    private function applyDataToProduct(Product $product, array $data): void
    {
        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        if (array_key_exists('description', $data)) {
            $product->setDescription($data['description']);
        }
        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }
        if (array_key_exists('type', $data)) {
            $product->setType($data['type'] !== '' ? $data['type'] : null);
        }
        if (array_key_exists('weight', $data)) {
            $product->setWeight($data['weight'] !== '' ? (float)$data['weight'] : null);
        }
        if (array_key_exists('height', $data)) {
            $product->setHeight($data['height'] !== '' ? (float)$data['height'] : null);
        }
        if (array_key_exists('width', $data)) {
            $product->setWidth($data['width'] !== '' ? (float)$data['width'] : null);
        }
        if (array_key_exists('downloadUrl', $data)) {
            $product->setDownloadUrl($data['downloadUrl'] !== '' ? $data['downloadUrl'] : null);
        }
        if (array_key_exists('licenseKey', $data)) {
            $product->setLicenseKey($data['licenseKey'] !== '' ? $data['licenseKey'] : null);
        }
    }
}

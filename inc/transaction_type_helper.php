<?php
/**
 * Shared helper functions for determining transaction types based on cart/order items.
 *
 * These helpers are intentionally lightweight so they can be reused
 * by customer-facing pages and backend classes alike.
 */
if (!function_exists('is_genuine_oil_item')) {
    /**
     * Determine if an item should be treated as a genuine oil product.
     *
     * @param string|null $category
     * @param string|null $productName
     * @return bool
     */
    function is_genuine_oil_item($category, $productName) {
        $category = strtolower(trim((string)$category));
        $productName = strtolower(trim((string)$productName));

        $keywords = ['genuine oil', 'oil', 'hgmo', 'lubricant', 'engine oil'];
        foreach ($keywords as $keyword) {
            if (strpos($category, $keyword) !== false || strpos($productName, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('is_motorcycle_part_item')) {
    /**
     * Determine if an item should be treated as a motorcycle part or accessory.
     *
     * @param string|null $category
     * @param string|null $productName
     * @return bool
     */
    function is_motorcycle_part_item($category, $productName) {
        $category = strtolower(trim((string)$category));
        $productName = strtolower(trim((string)$productName));

        $keywords = ['part', 'parts', 'accessor', 'gear', 'spare', 'tire', 'helmet'];
        foreach ($keywords as $keyword) {
            if (strpos($category, $keyword) !== false || strpos($productName, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('is_motorcycle_item')) {
    /**
     * Determine if an item should be treated as a motorcycle unit.
     *
     * @param string|null $category
     * @param string|null $productName
     * @return bool
     */
    function is_motorcycle_item($category, $productName) {
        $category = strtolower(trim((string)$category));
        $productName = strtolower(trim((string)$productName));

        $keywords = ['motorcycle', 'bike', 'scooter', 'unit'];
        foreach ($keywords as $keyword) {
            if (strpos($category, $keyword) !== false || strpos($productName, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('classify_transaction_item')) {
    /**
     * Classify a single item to identify how it should influence the transaction type.
     *
     * @param string|null $category
     * @param string|null $productName
     * @return array{is_motorcycle: bool, is_parts: bool, is_genuine_oil: bool}
     */
    function classify_transaction_item($category, $productName) {
        $isGenuineOil = is_genuine_oil_item($category, $productName);
        $isParts = !$isGenuineOil && is_motorcycle_part_item($category, $productName);
        $isMotorcycle = !$isGenuineOil && !$isParts && is_motorcycle_item($category, $productName);

        return [
            'is_motorcycle' => $isMotorcycle,
            'is_parts' => $isParts,
            'is_genuine_oil' => $isGenuineOil,
        ];
    }
}

if (!function_exists('resolve_transaction_type')) {
    /**
     * Resolve the transaction type slug based on item composition flags.
     *
     * @param bool $hasMotorcycle
     * @param bool $hasParts
     * @param bool $hasGenuineOil
     * @return string
     */
    function resolve_transaction_type($hasMotorcycle, $hasParts, $hasGenuineOil) {
        if ($hasMotorcycle) {
            return 'motorcycle_purchase';
        }
        if ($hasParts) {
            return 'motorcycle_parts_purchase';
        }
        if ($hasGenuineOil) {
            return 'genuine_oil_purchase';
        }
        return 'motorcycle_purchase';
    }
}


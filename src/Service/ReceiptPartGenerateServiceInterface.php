<?php
/**
 * 24.06.2020.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\ReceiptPart;

interface ReceiptPartGenerateServiceInterface
{
    /**
     * @param string $ingredient
     *
     * @return ReceiptPart
     */
    public function generate(string $ingredient): ReceiptPart;
}

<?php

namespace App\Services;

use App\Models\Order;

class FraudDetectionService
{
    /**
     * Mengecek fraud berdasarkan history (dioptimasi agar menghindari N+1 bila dipanggil via array/batch)
     * 
     * @param string|null $buyerPhone
     * @param string $shippingAddress
     * @return array{score:int,status:string,logs:array<int,array{rule_key:string,rule_label:string,points:int,notes:string}>}
     */
    public function calculate(?string $buyerPhone, string $shippingAddress): array
    {
        return $this->calculateWithContext([], [], $buyerPhone, $shippingAddress);
    }

    /**
     * @param  array<string, true>  $knownPhones
     * @param  array<string, true>  $knownAddresses
     * @return array{score:int,status:string,logs:array<int,array{rule_key:string,rule_label:string,points:int,notes:string}>}
     */
    public function calculateFromSets(
        array $knownPhones,
        array $knownAddresses,
        ?string $buyerPhone,
        string $shippingAddress
    ): array {
        $score = 0;
        $logs = [];

        if ($buyerPhone !== null && $buyerPhone !== '' && isset($knownPhones[$buyerPhone])) {
            $score += 1;
            $logs[] = $this->createLog(
                'same_buyer_phone',
                'Nomor HP pembeli sama dengan order lain',
                1,
                'Ditemukan order lain dengan nomor HP pembeli yang sama.'
            );
        }

        if (isset($knownAddresses[$shippingAddress])) {
            $score += 1;
            $logs[] = $this->createLog(
                'same_shipping_address',
                'Alamat pengiriman sama dengan order lain',
                1,
                'Ditemukan order lain dengan alamat pengiriman identik.'
            );
        }

        $status = match (true) {
            $score <= 1 => 'valid',
            $score <= 3 => 'suspicious',
            default => 'fraud',
        };

        return [
            'score' => $score,
            'status' => $status,
            'logs' => $logs,
        ];
    }

    /**
     * @param  array<string, true>  $knownPhones
     * @param  array<string, true>  $knownAddresses
     * @return array{score:int,status:string,logs:array<int,array{rule_key:string,rule_label:string,points:int,notes:string}>}
     */
    public function calculateWithContext(
        array $knownPhones,
        array $knownAddresses,
        ?string $buyerPhone,
        string $shippingAddress
    ): array {
        $score = 0;
        $logs = [];

        $phoneExists = $buyerPhone !== null && $buyerPhone !== ''
            && (
                isset($knownPhones[$buyerPhone])
                || Order::where('buyer_phone', $buyerPhone)->exists()
            );

        if ($phoneExists) {
            $score += 1;
            $logs[] = $this->createLog(
                'same_buyer_phone',
                'Nomor HP pembeli sama dengan order lain',
                1,
                'Ditemukan order lain dengan nomor HP pembeli yang sama.'
            );
        }

        $addressExists = isset($knownAddresses[$shippingAddress])
            || Order::where('shipping_address', $shippingAddress)->exists();

        if ($addressExists) {
            $score += 1;
            $logs[] = $this->createLog(
                'same_shipping_address',
                'Alamat pengiriman sama dengan order lain',
                1,
                'Ditemukan order lain dengan alamat pengiriman identik.'
            );
        }

        $status = match (true) {
            $score <= 1 => 'valid',
            $score <= 3 => 'suspicious',
            default => 'fraud',
        };

        return [
            'score' => $score,
            'status' => $status,
            'logs' => $logs,
        ];
    }

    private function createLog(string $key, string $label, int $points, string $notes): array
    {
        return [
            'rule_key' => $key,
            'rule_label' => $label,
            'points' => $points,
            'notes' => $notes,
        ];
    }
}

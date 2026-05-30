<?php

namespace App\Services;

use App\Models\Order;

class FraudImportContext
{
    /** @var array<string, true> */
    private array $orderNumbers = [];

    /** @var array<string, true> */
    private array $phones = [];

    /** @var array<string, true> */
    private array $addresses = [];

    public static function fromDatabase(): self
    {
        $context = new self;

        foreach (Order::query()->pluck('order_number') as $orderNumber) {
            $context->orderNumbers[$orderNumber] = true;
        }

        foreach (Order::query()->whereNotNull('buyer_phone')->where('buyer_phone', '!=', '')->pluck('buyer_phone') as $phone) {
            $context->phones[$phone] = true;
        }

        foreach (Order::query()->pluck('shipping_address') as $address) {
            $context->addresses[$address] = true;
        }

        return $context;
    }

    public function isDuplicateOrder(string $orderNumber): bool
    {
        return isset($this->orderNumbers[$orderNumber]);
    }

    public function register(string $orderNumber, ?string $phone, string $address): void
    {
        $this->orderNumbers[$orderNumber] = true;

        if ($phone !== null && $phone !== '') {
            $this->phones[$phone] = true;
        }

        $this->addresses[$address] = true;
    }

    /**
     * @return array{score:int,status:string,logs:array<int,array{rule_key:string,rule_label:string,points:int,notes:string}>}
     */
    public function calculate(FraudDetectionService $service, ?string $phone, string $address): array
    {
        return $service->calculateFromSets($this->phones, $this->addresses, $phone, $address);
    }
}

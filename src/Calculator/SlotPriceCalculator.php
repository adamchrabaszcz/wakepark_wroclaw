<?php

namespace App\Calculator;

use App\Entity\Slot;

class SlotPriceCalculator
{
    private array $prices;

    public function __construct(
        array $prices
    ) {
        $this->prices = $prices;
    }

    public function calculateSlotPrice(Slot $slot): int
    {
        $slotPrice = $slot->getBeginAt()->format("N") > 5
            ? $this->prices['weekend']
            : $this->prices['weekday'];

        $slotCount = abs($slot->getEndAt()->getTimestamp() - $slot->getBeginAt()->getTimestamp()) / 60 / 15;

        foreach ($slot->getOptions() as $option) {
            $slotPrice += $option->getPrice();
        }

        return $slotPrice * $slotCount;
    }
}
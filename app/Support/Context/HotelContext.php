<?php

namespace App\Support\Context;

final class HotelContext
{
    public function __construct(private ?int $hotelId = null)
    {
    }

    public function setHotelId(?int $hotelId): void
    {
        $this->hotelId = $hotelId;
    }

    public function hotelId(): ?int
    {
        return $this->hotelId;
    }
}

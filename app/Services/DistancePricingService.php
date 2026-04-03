<?php

namespace App\Services;

use App\Models\Outlet;

class DistancePricingService
{
    public function calculatePickup(Outlet $outlet, ?float $latitude, ?float $longitude): array
    {
        return $this->calculate($outlet, 'pickup', $latitude, $longitude);
    }

    public function calculateDelivery(Outlet $outlet, ?float $latitude, ?float $longitude): array
    {
        return $this->calculate($outlet, 'delivery', $latitude, $longitude);
    }

    public function calculate(Outlet $outlet, string $type, ?float $latitude, ?float $longitude): array
    {
        $enabledKey = "{$type}_enabled";
        $baseDistanceKey = "{$type}_base_distance_km";
        $baseFeeKey = "{$type}_base_fee";
        $extraFeeKey = "{$type}_extra_fee_per_km";
        $legacyFeeKey = "{$type}_fee";

        $isEnabled = (bool) ($outlet->{$enabledKey} ?? false);
        $baseDistanceKm = round((float) ($outlet->{$baseDistanceKey} ?? 0), 2);
        $baseFee = (int) ($outlet->{$baseFeeKey} ?? $outlet->{$legacyFeeKey} ?? 0);
        $extraFeePerKm = (int) ($outlet->{$extraFeeKey} ?? 0);
        $distanceKm = $this->distanceKm(
            $outlet->latitude,
            $outlet->longitude,
            $latitude,
            $longitude,
        );

        if (!$isEnabled) {
            return [
                'enabled' => false,
                'distance_km' => null,
                'base_distance_km' => $baseDistanceKm,
                'base_fee' => $baseFee,
                'extra_distance_km' => 0,
                'extra_fee_per_km' => $extraFeePerKm,
                'extra_fee_total' => 0,
                'final_fee' => 0,
            ];
        }

        if ($distanceKm === null) {
            return [
                'enabled' => true,
                'distance_km' => null,
                'base_distance_km' => $baseDistanceKm,
                'base_fee' => $baseFee,
                'extra_distance_km' => 0,
                'extra_fee_per_km' => $extraFeePerKm,
                'extra_fee_total' => 0,
                'final_fee' => $baseFee,
            ];
        }

        $extraDistanceKm = max(0, $distanceKm - $baseDistanceKm);
        $roundedExtraKm = $extraDistanceKm > 0 ? (int) ceil($extraDistanceKm) : 0;
        $extraFeeTotal = $roundedExtraKm * $extraFeePerKm;

        return [
            'enabled' => true,
            'distance_km' => round($distanceKm, 2),
            'base_distance_km' => $baseDistanceKm,
            'base_fee' => $baseFee,
            'extra_distance_km' => round($extraDistanceKm, 2),
            'extra_fee_per_km' => $extraFeePerKm,
            'extra_fee_total' => $extraFeeTotal,
            'final_fee' => $baseFee + $extraFeeTotal,
        ];
    }

    public function distanceKm(
        ?float $originLatitude,
        ?float $originLongitude,
        ?float $destinationLatitude,
        ?float $destinationLongitude
    ): ?float {
        if (
            $originLatitude === null ||
            $originLongitude === null ||
            $destinationLatitude === null ||
            $destinationLongitude === null
        ) {
            return null;
        }

        $earthRadiusKm = 6371;
        $deltaLatitude = deg2rad($destinationLatitude - $originLatitude);
        $deltaLongitude = deg2rad($destinationLongitude - $originLongitude);

        $a = sin($deltaLatitude / 2) ** 2
            + cos(deg2rad($originLatitude))
            * cos(deg2rad($destinationLatitude))
            * sin($deltaLongitude / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}

<?php

namespace App\Enums;

class ServiceCategory
{
    const PLUMBING = 'Plumbing';
    const ELECTRICAL = 'Electrical';
    const CLEANING = 'Cleaning';
    const BEAUTY = 'Beauty';
    const AC_REPAIR = 'AC Repair';
    const CAR_WASH = 'Car Wash';
    const HOME_APPLIANCE_REPAIR = 'Home Appliance Repair';
    const GARDENING = 'Gardening';
    const HOUSE_PAINTING = 'House Painting';
    const PEST_CONTROL = 'Pest Control';
    const MOVING_SERVICES = 'Moving Services';
    const LOCKSMITH = 'Locksmith';
    const CARPENTRY = 'Carpentry';
    const INTERIOR_DESIGN = 'Interior Design';
    const LAUNDRY = 'Laundry';
    const TUTORING = 'Tutoring';
    const PET_CARE = 'Pet Care';
    const BABYSITTING = 'Babysitting';
    const PERSONAL_TRAINING = 'Personal Training';
    const TAILORING = 'Tailoring';

    public static function cases(): array
    {
        return [
            self::PLUMBING,
            self::ELECTRICAL,
            self::CLEANING,
            self::BEAUTY,
            self::AC_REPAIR,
            self::CAR_WASH,
            self::HOME_APPLIANCE_REPAIR,
            self::GARDENING,
            self::HOUSE_PAINTING,
            self::PEST_CONTROL,
            self::MOVING_SERVICES,
            self::LOCKSMITH,
            self::CARPENTRY,
            self::INTERIOR_DESIGN,
            self::LAUNDRY,
            self::TUTORING,
            self::PET_CARE,
            self::BABYSITTING,
            self::PERSONAL_TRAINING,
            self::TAILORING,
        ];
    }

    public static function random(): string
    {
        return self::cases()[array_rand(self::cases())];
    }

    public static function values(): array
    {
        return self::cases();
    }
}


<?php

use App\Models\IntegraHub\IntegrationType;
use Illuminate\Support\Carbon;

describe('Scheduling interval is due method', function () {
    test('should be false when interval is not set', function () {
        $integrationType = new IntegrationType();
        expect($integrationType->isDue())->toBeFalse();
    });

    test('should be false when interval is 0', function () {
        $integrationType = new IntegrationType();
        $integrationType->interval = 0;
        expect($integrationType->isDue())->toBeFalse();
    });

    test('should be true when interval is set and last_run_at is null', function () {
        $integrationType = new IntegrationType();
        $integrationType->interval = 10;
        expect($integrationType->isDue())->toBeTrue();
    });

    test('should be false when last_run_at diff in minutes is lower than interval', function () {
        $integrationType = new IntegrationType();
        $integrationType->interval = 10;
        $integrationType->last_run_at = Carbon::now()->subMinutes(5);
        expect($integrationType->isDue())->toBeFalse();
    });

    test('should be true when last_run_at diff in minutes is greater than interval', function () {
        $integrationType = new IntegrationType();
        $integrationType->interval = 10;
        $integrationType->last_run_at = Carbon::now()->subMinutes(20);
        expect($integrationType->isDue())->toBeTrue();
    });
});

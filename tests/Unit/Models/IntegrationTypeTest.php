<?php

use App\Models\IntegraHub\IntegrationType;
use Illuminate\Support\Carbon;

describe('Scheduling settings is due method', function () {
    test('should be false when scheduling settings is not set', function () {
        $integrationType = new IntegrationType();
        expect($integrationType->isDue())->toBeFalse();
    });

    test('should be false when scheduling settings is set but frequency is not', function () {
        $integrationType = new IntegrationType();
        $integrationType->scheduling_settings = [];
        expect($integrationType->isDue())->toBeFalse();
    });

    test('should be true for hourly schedule and current time is at 00 minutes', function () {
        $integrationType = new IntegrationType();
        $integrationType->scheduling_settings = [
            'frequency' => 'hourly',
        ];
        Carbon::setTestNow(Carbon::parse('2024-02-20 10:00:00'));
        expect($integrationType->isDue())->toBeTrue();
    });

    test('should be false for hourly schedule and current time is not at 00 minutes', function () {
        $integrationType = new IntegrationType();
        $integrationType->scheduling_settings = [
            'frequency' => 'hourly',
        ];
        Carbon::setTestNow(Carbon::parse('2024-02-20 10:18:00'));
        expect($integrationType->isDue())->toBeFalse();
    });

    test('should be true for daily schedule and current time matches the scheduled time', function () {
        $integrationType = new IntegrationType();
        $integrationType->scheduling_settings = [
            'frequency' => 'daily',
            'time' => '10:00:00',
        ];
        Carbon::setTestNow(Carbon::parse('2024-02-20 10:00:00'));
        expect($integrationType->isDue())->toBeTrue();
    });

    test('should be false for daily schedule and current time does not matches the scheduled time', function () {
        $integrationType = new IntegrationType();
        $integrationType->scheduling_settings = [
            'frequency' => 'daily',
            'time' => '11:00:00',
        ];
        Carbon::setTestNow(Carbon::parse('2024-02-20 10:00:00'));
        expect($integrationType->isDue())->toBeFalse();
    });

    test('should be true for custom schedule and the cron expression is due', function () {
        $integrationType = new IntegrationType();
        $integrationType->scheduling_settings = [
            'frequency' => 'custom',
            'expression' => '0 10 * * *',
        ];
        Carbon::setTestNow(Carbon::parse('2024-02-20 10:00:00'));
        expect($integrationType->isDue())->toBeTrue();
    });

    test('should be false for custom schedule and the cron expression is not due', function () {
        $integrationType = new IntegrationType();
        $integrationType->scheduling_settings = [
            'frequency' => 'custom',
            'expression' => '0 11 * * *',
        ];
        Carbon::setTestNow(Carbon::parse('2024-02-20 10:00:00'));
        expect($integrationType->isDue())->toBeFalse();
    });
});

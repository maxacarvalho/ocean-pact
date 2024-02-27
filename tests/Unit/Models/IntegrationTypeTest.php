<?php

use App\Models\IntegraHub\IntegrationType;
use Illuminate\Support\Carbon;
use Mockery;

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
        $integrationType = Mockery::mock(IntegrationType::class, function ($mock) {
            $mock->shouldReceive('getAttribute')->with('interval')->andReturn(10);
            $mock->shouldReceive('getAttribute')->with('last_run_at')->andReturn(Carbon::now()->subMinutes(5));
        })->makePartial();
        expect($integrationType->isDue())->toBeFalse();
    });

    test('should be true when last_run_at diff in minutes is greater than interval', function () {
        $integrationType = Mockery::mock(IntegrationType::class, function ($mock) {
            $mock->shouldReceive('getAttribute')->with('interval')->andReturn(10);
            $mock->shouldReceive('getAttribute')->with('last_run_at')->andReturn(Carbon::now()->subMinutes(20));
        })->makePartial();
        expect($integrationType->isDue())->toBeTrue();
    });
});

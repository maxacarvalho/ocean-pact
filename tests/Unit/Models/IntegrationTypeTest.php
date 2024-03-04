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

describe('getAuthorizationHeader method', function () {
    test('should return empty when authorization is not set', function () {
        $integrationType = new IntegrationType();
        expect($integrationType->getAuthorizationHeader())->toBeEmpty();
    });

    test('should return basic auth header when authorization is set to basic', function () {
        $integrationType = new IntegrationType();
        $integrationType->authorization = [
            'type' => 'basic',
            'username' => 'test',
            'password' => 'test',
        ];
        $basicAuth = 'Basic '.base64_encode('test:test');
        expect($integrationType->getAuthorizationHeader())->toBeArray();
        expect($integrationType->getAuthorizationHeader()['Authorization'])->toBe($basicAuth);
    });
});

describe('getHeaders method', function () {
    test('should return empty header is not set', function () {
        $integrationType = new IntegrationType();
        expect($integrationType->getHeaders())->toBeEmpty();
    });

    test('should return array of headers when they are set', function () {
        $integrationType = new IntegrationType();
        $integrationType->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $headers = $integrationType->getHeaders();
        expect($headers)->toBeArray();
        expect($headers['Accept'])->toBe('application/json');
        expect($headers['Content-Type'])->toBe('application/json');
    });

    test('should include authorization headers', function () {
        $integrationType = new IntegrationType();
        $integrationType->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $integrationType->authorization = [
            'type' => 'basic',
            'username' => 'test',
            'password' => 'test',
        ];
        $headers = $integrationType->getHeaders();
        expect($headers)->toBeArray();
        expect($headers['Accept'])->toBe('application/json');
        expect($headers['Content-Type'])->toBe('application/json');
        expect($headers['Authorization'])->toBe('Basic '.base64_encode('test:test'));
    });
});

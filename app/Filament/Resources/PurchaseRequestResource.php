<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseRequestResource\Pages\ListPurchaseRequests;
use App\Models\Budget;
use App\Models\Company;
use App\Models\PurchaseRequest;
use App\Models\Quote;
use App\Utils\Str;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PurchaseRequestResource extends Resource
{
    protected static ?string $model = PurchaseRequest::class;

    protected static ?string $navigationIcon = 'far-cart-circle-check';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('purchase_request.purchase_requests'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('purchase_request.purchase_request'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('purchase_request.purchase_requests'));
    }

    public static function getNavigationGroup(): ?string
    {
        return Str::formatTitle(__('navigation.quotes'));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(PurchaseRequest::RELATION_QUOTE.'.'.Quote::RELATION_COMPANY.'.'.Company::CODE_CODE_BRANCH_AND_BUSINESS_NAME)
                    ->label(Str::ucfirst(__('purchase_request.company'))),

                TextColumn::make(PurchaseRequest::RELATION_QUOTE.'.'.Quote::RELATION_BUDGET.'.'.Budget::BUDGET_NUMBER)
                    ->label(Str::ucfirst(__('purchase_request.budget'))),

                TextColumn::make(PurchaseRequest::RELATION_QUOTE.'.'.Quote::QUOTE_NUMBER)
                    ->label(Str::ucfirst(__('purchase_request.quote'))),

                TextColumn::make(PurchaseRequest::PURCHASE_REQUEST_NUMBER)
                    ->label(Str::ucfirst(__('purchase_request.purchase_request_number'))),

                TextColumn::make(PurchaseRequest::SENT_AT)
                    ->label(Str::ucfirst(__('purchase_request.sent_at')))
                    ->dateTime('d/m/Y H:i'),

                TextColumn::make(PurchaseRequest::VIEWED_AT)
                    ->label(Str::ucfirst(__('purchase_request.viewed_at')))
                    ->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                Action::make('download_file')
                    ->label(Str::ucfirst(__('purchase_request.download_file')))
                    ->icon('fas-download')
                    ->action(function (PurchaseRequest $record) {
                        $filePath = "purchase_request_files/{$record->purchase_request_number}.pdf";
                        Storage::disk('local')->put($filePath, base64_decode($record->file));
                        $record->update([PurchaseRequest::VIEWED_AT => now()]);

                        return Storage::disk('local')->download($filePath);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseRequests::route('/'),
        ];
    }
}

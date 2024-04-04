<?php

namespace App\Filament\Resources\IntegrationTypeResource\RelationManagers;

use App\Enums\IntegraHub\IntegrationTypeFieldTypeEnum;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\IntegrationTypeField;
use App\Utils\Str;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Actions\DeleteAction as TableDeleteAction;
use Filament\Tables\Actions\DeleteBulkAction as TableDeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Throwable;

class FieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    protected static ?string $recordTitleAttribute = 'field_name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return Str::title(__('integration_type_field.integration_type_fields'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make(Str::title(__('integration_type_field.general')))
                    ->schema([
                        TextInput::make(IntegrationTypeField::FIELD_NAME)
                            ->label(Str::title(__('integration_type_field.field_name')))
                            ->rules([
                                'required',
                                $this->getUniqueRule($form, IntegrationTypeField::FIELD_NAME),
                            ]),

                        Select::make(IntegrationTypeField::FIELD_TYPE)
                            ->label(Str::title(__('integration_type_field.field_type')))
                            ->required()
                            ->live()
                            ->options(IntegrationTypeFieldTypeEnum::class)
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set(
                                    path: IntegrationTypeField::FIELD_RULES.'.array',
                                    state: $this->isFieldType($state, IntegrationTypeFieldTypeEnum::array)
                                );
                            }),

                        Select::make('target_integration')
                            ->label(Str::title(__('integration_type_field.target_integration')))
                            ->options(fn () => $this->getTargetIntegration())
                            ->default($this->record?->targetIntegrationTypeField?->integration_type_id ?? 0)
                            ->live(),

                        Select::make(IntegrationTypeField::TARGET_INTEGRATION_TYPE_FIELD_ID)
                            ->label(Str::title(__('integration_type_field.target_integration_type_field')))
                            ->options(fn (Get $get) => $get('target_integration') ? $this->getTargetFields($get('target_integration')) : [])
                            ->default($this->record?->target_integration_type_field_id ?? 0)
                            ->preload(),
                    ]),

                Fieldset::make(Str::title(__('integration_type_field.common_rules')))
                    ->columns(3)
                    ->schema([
                        Toggle::make(IntegrationTypeField::FIELD_RULES.'.required')
                            ->label(Str::title(__('integration_type_field.required')))
                            ->default(true)
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set(IntegrationTypeField::FIELD_RULES.'.nullable', ! $state);
                            }),

                        Toggle::make(IntegrationTypeField::FIELD_RULES.'.nullable')
                            ->label(Str::title(__('integration_type_field.nullable'))),

                        Toggle::make(IntegrationTypeField::FIELD_RULES.'.present')
                            ->label(Str::title(__('integration_type_field.present'))),

                        Toggle::make(IntegrationTypeField::FIELD_RULES.'.sometimes')
                            ->label(Str::title(__('integration_type_field.optional'))),

                        Hidden::make(IntegrationTypeField::FIELD_RULES.'.array')
                            ->label(Str::title(__('integration_type_field.array'))),
                    ]),

                Fieldset::make(Str::title(__('integration_type_field.numeric_rules')))
                    ->visible(function (Get $get) {
                        return $this->isFieldType($get(IntegrationTypeField::FIELD_TYPE), IntegrationTypeFieldTypeEnum::date);
                    })
                    ->schema([
                        TextInput::make(IntegrationTypeField::FIELD_RULES.'.date_format')
                            ->label(Str::title(__('integration_type_field.date_format')))
                            ->helperText(function () {
                                return new HtmlString('<a href="https://www.php.net/manual/pt_BR/datetime.formats.php" target="_blank">'.Str::lcfirst(__('integration_type_field.date_format_helper_text')).'</a>');
                            })
                            ->required(function (Get $get) {
                                return $this->isFieldType(
                                    $get(IntegrationTypeField::FIELD_TYPE),
                                    IntegrationTypeFieldTypeEnum::date
                                );
                            }),
                    ]),

                Fieldset::make(Str::title(__('integration_type_field.array_rules')))
                    ->visible(function (Get $get) {
                        return $this->isFieldType($get(IntegrationTypeField::FIELD_TYPE), IntegrationTypeFieldTypeEnum::array);
                    })
                    ->schema([
                        TextInput::make(IntegrationTypeField::FIELD_RULES.'.size')
                            ->label(Str::title(__('integration_type_field.array_size'))),
                    ]),

                Fieldset::make(Str::title(__('integration_type_field.numeric_rules')))
                    ->visible(function (Get $get) {
                        return $this->isFieldType($get(IntegrationTypeField::FIELD_TYPE), IntegrationTypeFieldTypeEnum::float)
                            || $this->isFieldType($get(IntegrationTypeField::FIELD_TYPE), IntegrationTypeFieldTypeEnum::integer);
                    })
                    ->schema([
                        TextInput::make(IntegrationTypeField::FIELD_RULES.'.digits')
                            ->label(Str::title(__('integration_type_field.digits')))
                            ->numeric(),

                        TextInput::make(IntegrationTypeField::FIELD_RULES.'.digits_between')
                            ->label(Str::title(__('integration_type_field.digits_between')))
                            ->helperText(Str::lcfirst(__('integration_type_field.digits_between_helper_text')))
                            ->regex('/^([0-9]+),([0-9]+)$/'),

                        TextInput::make(IntegrationTypeField::FIELD_RULES.'.size')
                            ->label(Str::title(__('integration_type_field.numeric_or_file_size'))),
                    ]),

                Fieldset::make(Str::title(__('integration_type_field.string_rules')))
                    ->visible(fn (Get $get) => $this->isFieldType($get(IntegrationTypeField::FIELD_TYPE), IntegrationTypeFieldTypeEnum::string))
                    ->schema([
                        Toggle::make(IntegrationTypeField::FIELD_RULES.'.email')
                            ->label(Str::title(__('integration_type_field.email'))),

                        Toggle::make(IntegrationTypeField::FIELD_RULES.'.alpha')
                            ->label(Str::title(__('integration_type_field.alpha'))),

                        Toggle::make(IntegrationTypeField::FIELD_RULES.'.alpha_num')
                            ->label(Str::title(__('integration_type_field.alpha_num'))),

                        Toggle::make(IntegrationTypeField::FIELD_RULES.'.alpha_dash')
                            ->label(Str::title(__('integration_type_field.alpha_dash'))),

                        Toggle::make(IntegrationTypeField::FIELD_RULES.'.lowercase')
                            ->label(Str::title(__('integration_type_field.lowercase'))),

                        Toggle::make(IntegrationTypeField::FIELD_RULES.'.uppercase')
                            ->label(Str::title(__('integration_type_field.uppercase'))),

                        TextInput::make(IntegrationTypeField::FIELD_RULES.'.starts_with')
                            ->label(Str::title(__('integration_type_field.starts_with')))
                            ->helperText(Str::lcfirst(__('integration_type_field.starts_with_helper_text'))),

                        TextInput::make(IntegrationTypeField::FIELD_RULES.'.size')
                            ->label(Str::title(__('integration_type_field.string_size'))),

                        TagsInput::make(IntegrationTypeField::FIELD_RULES.'.in')
                            ->label(Str::title(__('integration_type_field.in')))
                            ->placeholder(null)
                            ->separator(','),
                    ]),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel(fn () => Str::title(__('integration_type_field.integration_type_field')))
            ->pluralModelLabel(fn () => Str::title(__('integration_type_field.integration_type_fields')))
            ->reorderable(IntegrationTypeField::ORDER_COLUMN)
            ->defaultSort(IntegrationTypeField::ORDER_COLUMN)
            ->columns([
                TextColumn::make(IntegrationTypeField::FIELD_NAME)
                    ->label(Str::title(__('integration_type_field.field_name'))),
                TextColumn::make(IntegrationTypeField::FIELD_TYPE)
                    ->label(Str::title(__('integration_type_field.field_type'))),
                TextColumn::make(IntegrationTypeField::TARGET_INTEGRATION_TYPE_FIELD_ID)
                    ->label(Str::title(__('integration_type_field.target_integration_type_field')))
                    ->formatStateUsing(function (IntegrationTypeField $record) {
                        $record->load('targetIntegrationTypeField');

                        return $record->targetIntegrationTypeField?->field_name ?? '';
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                TableCreateAction::make()->mutateFormDataUsing(fn (array $data) => self::prepareData($data)),
            ])
            ->actions([
                TableEditAction::make()->mutateFormDataUsing(fn (array $data) => self::prepareData($data)),
                TableDeleteAction::make(),
            ])
            ->bulkActions([
                TableDeleteBulkAction::make(),
            ]);
    }

    private static function prepareData(array $data): array
    {
        $data[IntegrationTypeField::FIELD_RULES] = array_filter($data[IntegrationTypeField::FIELD_RULES]);

        $fieldType = $data[IntegrationTypeField::FIELD_TYPE];

        if ($fieldType === IntegrationTypeFieldTypeEnum::float) {
            unset($data[IntegrationTypeField::FIELD_RULES]['numeric']);
            $data[IntegrationTypeField::FIELD_RULES] = ['numeric' => true] + $data[IntegrationTypeField::FIELD_RULES];
        }

        if ($fieldType === IntegrationTypeFieldTypeEnum::integer) {
            unset($data[IntegrationTypeField::FIELD_RULES]['integer']);
            $data[IntegrationTypeField::FIELD_RULES] = ['integer' => true] + $data[IntegrationTypeField::FIELD_RULES];
        }

        if ($fieldType === IntegrationTypeFieldTypeEnum::boolean) {
            unset($data[IntegrationTypeField::FIELD_RULES]['boolean']);
            $data[IntegrationTypeField::FIELD_RULES] = ['boolean' => true] + $data[IntegrationTypeField::FIELD_RULES];
        }

        if ($fieldType === IntegrationTypeFieldTypeEnum::string) {
            unset($data[IntegrationTypeField::FIELD_RULES]['string']);
            $data[IntegrationTypeField::FIELD_RULES] = ['string' => true] + $data[IntegrationTypeField::FIELD_RULES];
        }

        unset($data['target_integration']);

        return $data;
    }

    private function isFieldType(mixed $fieldType, $type): bool
    {
        try {
            if ($fieldType instanceof IntegrationTypeFieldTypeEnum) {
                return $fieldType->equals($type);
            }

            $fieldType = IntegrationTypeFieldTypeEnum::from($fieldType);

            return $fieldType->equals($type);
        } catch (Throwable) {
            return false;
        }
    }

    private function getUniqueRule(Form $form, string $field): Unique
    {
        return Rule::unique(IntegrationTypeField::TABLE_NAME, $field)
            ->ignore($form->getRecord()?->id)
            ->where(IntegrationTypeField::INTEGRATION_TYPE_ID, $this->getOwnerRecord()->id);
    }

    private function getTargetIntegration(): Collection
    {
        return Collection::make(IntegrationType::query()->pluck(IntegrationType::CODE, IntegrationType::ID));
    }

    private function getTargetFields(int $integrationTypeId): Collection
    {
        return Collection::make(
            IntegrationTypeField::query()
                ->where(IntegrationTypeField::INTEGRATION_TYPE_ID, $integrationTypeId)
                ->pluck(IntegrationTypeField::FIELD_NAME, IntegrationTypeField::ID)
        );
    }
}

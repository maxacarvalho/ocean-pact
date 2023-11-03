<?php

namespace App\Livewire;

use App\Enums\QuotesPortal\InvitationStatusEnum;
use App\Models\QuotesPortal\Supplier;
use App\Models\QuotesPortal\SupplierInvitation;
use App\Models\Role;
use App\Models\User;
use App\Utils\Str;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportRedirects\Redirector;

/**
 * @property Form $form
 */
class SupplierRegistration extends SimplePage
{
    use InteractsWithFormActions;
    use WithRateLimiting;

    #[Locked]
    public int $supplierId;

    #[Locked]
    public string $supplierName;

    #[Locked]
    public int $quoteId;

    protected static string $view = 'livewire.supplier-registration';

    public ?array $data = [];

    public function getTitle(): string|Htmlable
    {
        return Str::ucfirst(__('invitation.create_supplier_user_account'));
    }

    public function mount(string $token): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        /** @var SupplierInvitation $invitation */
        $invitation = SupplierInvitation::query()
            ->with([
                SupplierInvitation::RELATION_SUPPLIER,
                SupplierInvitation::RELATION_QUOTE,
            ])
            ->where(SupplierInvitation::TOKEN, '=', $token)
            ->where(SupplierInvitation::STATUS, '=', InvitationStatusEnum::SENT)
            ->firstOrFail();

        if (null === $invitation->supplier) {
            abort(404);
        }

        $this->supplierId = $invitation->supplier->id;
        $this->supplierName = $invitation->supplier->name;
        $this->quoteId = $invitation->quote_id;

        $this->form->fill();
    }

    public function register(): RedirectResponse|Redirector|null
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(
                    __('filament-panels::pages/auth/register.notifications.throttled.title', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ])
                )
                ->body(
                    array_key_exists('body', __('filament-panels::pages/auth/register.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/register.notifications.throttled.body', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ]) : null
                )
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        /** @var User $user */
        $user = User::query()->create(
            array_merge(
                $data,
                [
                    User::SUPPLIER_ID => $this->supplierId,
                ]
            )
        );

        $user->assignRole(Role::ROLE_SELLER);

        app()->bind(
            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
            \Filament\Listeners\Auth\SendEmailVerificationNotification::class,
        );
        event(new Registered($user));

        Filament::auth()->login($user);

        session()->regenerate();

        return redirect()->route('filament.admin.resources.quotes.edit', ['record' => $this->quoteId]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make(Supplier::NAME)
                    ->label(Str::ucfirst(__('supplier.supplier')))
                    ->content($this->supplierName),

                TextInput::make(User::NAME)
                    ->label(Str::ucfirst(__('user.name')))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                TextInput::make(User::EMAIL)
                    ->label(Str::ucfirst(__('user.email')))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(User::TABLE_NAME, User::EMAIL),

                TextInput::make(User::PASSWORD)
                    ->label(Str::ucfirst(__('user.password')))
                    ->password()
                    ->required()
                    ->rule(Password::default())
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->same('passwordConfirmation'),

                TextInput::make('passwordConfirmation')
                    ->label(Str::ucfirst(__('user.password_confirm')))
                    ->password()
                    ->required()
                    ->dehydrated(false),
            ])
            ->statePath('data');
    }

    public function loginAction(): Action
    {
        return Action::make('login')
            ->link()
            ->label(__('filament-panels::pages/auth/register.actions.login.label'))
            ->url(filament()->getLoginUrl());
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->label(__('filament-panels::pages/auth/register.form.actions.register.label'))
            ->submit('register');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction(),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}

<?php

namespace App\Livewire;

use App\Enums\QuotesPortal\InvitationStatusEnum;
use App\Models\QuotesPortal\BuyerInvitation;
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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportRedirects\Redirector;

/**
 * @property Form $form
 */
class BuyerRegistration extends SimplePage
{
    use InteractsWithFormActions;
    use WithRateLimiting;

    #[Locked]
    public string $email;

    #[Locked]
    public int $userId;

    #[Locked]
    public int $invitationId;

    protected static string $view = 'livewire.buyer-registration';

    public ?array $data = [];

    public function getTitle(): string|Htmlable
    {
        return Str::ucfirst(__('invitation.buyer_registration_page_title'));
    }

    public function mount(string $token): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        /** @var BuyerInvitation $invitation */
        $invitation = BuyerInvitation::query()
            ->with(BuyerInvitation::RELATION_BUYER)
            ->where(BuyerInvitation::TOKEN, '=', $token)
            ->where(BuyerInvitation::STATUS, '=', InvitationStatusEnum::SENT)
            ->firstOrFail();

        if (null === $invitation->buyer) {
            abort(404);
        }

        $this->invitationId = $invitation->id;
        $user = $invitation->buyer;
        $this->email = $user->email;
        $this->userId = $user->id;

        $this->form->fill([
            User::ID => $this->userId,
            User::EMAIL => $this->email,
            User::NAME => $user->name,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make(User::EMAIL)
                    ->label(Str::title(__('user.email')))
                    ->content($this->email),

                TextInput::make(User::NAME)
                    ->label(Str::title(__('user.name')))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                TextInput::make(User::PASSWORD)
                    ->label(Str::ucfirst(__('user.password')))
                    ->password()
                    ->required()
                    ->rule(Password::default())
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->same('password_confirm'),

                TextInput::make('password_confirm')
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

        /** @var BuyerInvitation $invitation */
        $invitation = BuyerInvitation::query()->findOrFail($this->invitationId);

        /** @var User $user */
        $user = User::query()->findOrFail($this->userId);

        $data = $this->form->getState();

        $user->password = $data[User::PASSWORD];
        $user->setRememberToken(Str::random(60));
        $user->is_draft = false;
        $user->save();

        $invitation->markAsAccepted();

        return redirect()->to(Filament::getPanel('admin')->getLoginUrl());
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

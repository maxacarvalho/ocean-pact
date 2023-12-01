<?php

namespace App\Livewire;

use App\Enums\QuotesPortal\InvitationStatusEnum;
use App\Models\QuotesPortal\UserInvitation;
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
class UserAccountActivation extends SimplePage
{
    use InteractsWithFormActions;
    use WithRateLimiting;

    #[Locked]
    public string $email;

    #[Locked]
    public int $userId;

    #[Locked]
    public int $invitationId;

    protected static string $view = 'livewire.account-activation';

    public ?array $data = [];

    public function getTitle(): string|Htmlable
    {
        return Str::ucfirst(__('invitation.account_registration_page_title'));
    }

    public function mount(string $token): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        /** @var UserInvitation $invitation */
        $invitation = UserInvitation::query()
            ->with([
                UserInvitation::RELATION_USER,
                UserInvitation::RELATION_QUOTE,
            ])
            ->where(UserInvitation::TOKEN, '=', $token)
            ->where(UserInvitation::STATUS, '=', InvitationStatusEnum::SENT)
            ->firstOrFail();

        if (null === $invitation->user) {
            abort(404);
        }

        $this->invitationId = $invitation->id;
        $user = $invitation->user;
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

    public function getActivateAccountFormAction(): Action
    {
        return Action::make('register')
            ->label(__('filament-panels::pages/auth/register.form.actions.register.label'))
            ->submit('register');
    }

    public function activate(): RedirectResponse|Redirector|null
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

        /** @var UserInvitation $invitation */
        $invitation = UserInvitation::query()->findOrFail($this->invitationId);

        /** @var User $user */
        $user = User::query()->findOrFail($this->userId);

        $data = $this->form->getState();

        $user->activateAccount($data[User::PASSWORD]);

        $invitation->markAsAccepted();

        if ($invitation->quote_id && $invitation->user->isSeller()) {
            return redirect()->route('filament.admin.resources.quotes.edit', ['record' => $invitation->quote_id]);
        }

        return redirect()->to(Filament::getPanel('admin')->getLoginUrl());
    }

    protected function getFormActions(): array
    {
        return [
            $this->getActivateAccountFormAction(),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}

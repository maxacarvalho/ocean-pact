<?php

namespace App\Http\Livewire;

use App\Enums\InvitationStatusEnum;
use App\Models\BuyerInvitation;
use App\Models\User;
use App\Utils\Str;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

/**
 * @property ComponentContainer $form
 */
class BuyerRegistrationLivewireComponent extends Component implements HasForms
{
    use InteractsWithForms;

    public $token;
    public User $user;
    public BuyerInvitation $invitation;
    public $name;
    public $email;
    public $password;
    public $password_confirm;

    public function mount(string $token): void
    {
        if (Filament::auth()->check()) {
            redirect(config('filament.home_url'));

            return;
        }

        $this->invitation = BuyerInvitation::query()
            ->with(BuyerInvitation::RELATION_BUYER)
            ->where(BuyerInvitation::TOKEN, '=', $token)
            ->where(BuyerInvitation::STATUS, '=', InvitationStatusEnum::SENT())
            ->firstOrFail();

        $this->user = $this->invitation->buyer;
        $this->email = $this->user->email;

        $this->form->fill([
            User::EMAIL => $this->user->email,
            User::NAME => $this->user->name,
        ]);
    }

    public function render(): View
    {
        $view = view('livewire.buyer-registration');

        $view->layout('filament::components.layouts.base', [
            'title' => Str::ucfirst(__('invitation.buyer_registration_page_title')),
        ]);

        return $view;
    }

    public function submit(): void
    {
        $user = $this->user;

        $user->password = Hash::make($this->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        $this->invitation->update([
            BuyerInvitation::STATUS => InvitationStatusEnum::ACCEPTED(),
            BuyerInvitation::REGISTERED_AT => now(),
        ]);

        Notification::make()
            ->title(Str::ucfirst(__('invitation.buyer_registration_notification_success')))
            ->success()
            ->send();

        redirect(route('filament.auth.login', ['email' => $this->email]));
    }

    protected function getFormSchema(): array
    {
        return [
            Placeholder::make(User::EMAIL)
                ->label(Str::title(__('user.email')))
                ->content($this->user->email),

            TextInput::make(User::NAME)
                ->label(Str::title(__('user.name')))
                ->required(),

            TextInput::make(User::PASSWORD)
                ->label(Str::title(__('user.password')))
                ->required()
                ->password(),
            TextInput::make('password_confirm')
                ->label(Str::title(__('user.password_confirm')))
                ->required()
                ->password()
                ->same(User::PASSWORD),
        ];
    }
}

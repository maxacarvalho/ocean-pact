<?php

namespace App\Http\Livewire;

use App\Enums\InvitationStatusEnum;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\SupplierInvitation;
use App\Models\User;
use App\Utils\Str;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

/**
 * @property ComponentContainer $form
 */
class SupplierInvitationLivewireComponent extends Component implements HasForms
{
    use InteractsWithForms;

    public $token;
    public Supplier $supplier;
    public SupplierInvitation|Model $invitation;
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

        $this->invitation = SupplierInvitation::query()
            ->with([
                SupplierInvitation::RELATION_SUPPLIER,
                SupplierInvitation::RELATION_QUOTE,
            ])
            ->where(SupplierInvitation::TOKEN, '=', $token)
            ->where(SupplierInvitation::STATUS, '=', InvitationStatusEnum::SENT())
            ->firstOrFail();

        if (null === $this->invitation->supplier) {
            abort(404);
        }

        $this->supplier = $this->invitation->supplier;
    }

    public function render(): View
    {
        $view = view('livewire.supplier-invitation');

        $view->layout('filament::components.layouts.base', [
            'title' => Str::ucfirst(__('invitation.buyer_registration_page_title')),
        ]);

        return $view;
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        /** @var User $user */
        $user = User::query()->create($data);

        $user->assignRole(Role::ROLE_SELLER);

        event(new Registered($user));

        Filament::auth()->login($user);

        redirect()->route('filament.resources.quotes.edit', ['record' => $this->invitation->quote_id]);
    }

    protected function getFormSchema(): array
    {
        return [
            Placeholder::make(Supplier::NAME)
                ->label(Str::title(__('supplier.supplier')))
                ->content($this->supplier->name),

            TextInput::make(User::NAME)
                ->label(Str::title(__('user.name')))
                ->required(),

            TextInput::make(User::EMAIL)
                ->label(Str::title(__('user.email')))
                ->required()
                ->unique(table: User::TABLE_NAME, column: User::EMAIL)
                ->email(),

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

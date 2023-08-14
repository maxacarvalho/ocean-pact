<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LocaleSwitcher extends Component
{
    public function render(): View
    {
        return view('livewire.locale-switcher');
    }

    public function locales(): array
    {
        $currentLocale = App::getLocale();

        return collect(config('ocean-pact.locales', []))
            ->filter(fn ($locale, $key) => $key !== $currentLocale)
            ->toArray();
    }

    public function setLocale(string $locale): void
    {
        session()->pull('locale', $locale);
        cookie()->queue(
            cookie()->forever('locale', $locale),
        );
        Auth::user()?->update(['locale' => $locale]);

        $this->redirect(request()->headers->get('referer'));
    }

    public function getCurrentLocaleFlag(): string
    {
        return $this->currentLocale()['flag'];
    }

    public function getCurrentLocaleLabel(): string
    {
        return $this->currentLocale()['label'];
    }

    /**
     * @return array{label: string, flag: string}
     */
    private function currentLocale(): array
    {
        return config('ocean-pact.locales')[App::getLocale()] ?? ['label' => 'Português', 'flag' => 'br'];
    }
}

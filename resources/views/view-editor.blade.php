<x-filament-panels::page>
    <style>
        .fi-env-key code {
            font-weight: 600;
            letter-spacing: 0.02em;
            word-break: break-all;
        }

        .fi-env-line code {
            color: rgb(var(--c-400));
            word-break: break-all;
            user-select: all;
        }

        .fi-env-row {
            border-bottom: 1px solid rgb(var(--gray-200));
            padding-block: 0.5rem;
        }

        .dark .fi-env-row {
            border-bottom-color: rgb(var(--gray-700));
        }

        .fi-env-security-score {
            margin-bottom: 1rem;
        }
    </style>

    <form wire:submit="save">
        {{ $this->form }}

        <x-filament::actions :actions="$this->getCachedFormActions()" :full-width="$this->hasFullWidthFormActions()" />
    </form>

    <x-filament-panels::unsaved-action-changes-alert />
</x-filament-panels::page>
<?php

namespace ArabicSooq\FilamentEnvEditor\Pages\Actions;

use ArabicSooq\FilamentEnvEditor\FilamentEnvEditorPlugin;
use ArabicSooq\FilamentEnvEditor\Pages\ViewEnv;
use ArabicSooq\FilamentEnvEditor\Support\EnvValidation;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Size;
use GeoSot\EnvEditor\Dto\EntryObj;
use GeoSot\EnvEditor\Facades\EnvEditor;

class DeleteAction extends Action
{
    use CanCustomizeProcess;

    private string $entryKey;

    public static function getDefaultName(): ?string
    {
        return 'delete';
    }

    public function setEntry(EntryObj $obj): static
    {
        $this->entryKey = $obj->key;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-trash');
        $this->hiddenLabel();
        $this->color(Color::Rose);
        $this->size(Size::Small);
        $this->modalIcon('heroicon-o-trash');
        $this->outlined();
        $this->requiresConfirmation();

        $this->tooltip(fn (): string => __(
            'filament-env-editor::filament-env-editor.actions.delete.tooltip',
            ['name' => $this->entryKey]
        ));

        $this->modalHeading(fn (): string => __(
            'filament-env-editor::filament-env-editor.actions.delete.confirm.title',
            ['name' => $this->entryKey]
        ));

        $this->action(function (ViewEnv $page) {
            if (!EnvValidation::canDeleteKey($this->entryKey, FilamentEnvEditorPlugin::get()->getProtectKeys())) {
                $this->failureNotificationTitle(
                    __('filament-env-editor::filament-env-editor.validation.delete.protected', ['key' => $this->entryKey])
                );
                $this->failure();
                $this->halt();

                return;
            }

            $result = EnvEditor::deleteKey($this->entryKey);

            $result ? $this->success() : $this->failure();

            $page->triggerRefresh();
        });
    }
}

<?php

namespace ArabicSooq\FilamentEnvEditor\Pages;

use ArabicSooq\FilamentEnvEditor\FilamentEnvEditorPlugin;
use ArabicSooq\FilamentEnvEditor\Pages\Actions\Backups\DeleteBackupAction;
use ArabicSooq\FilamentEnvEditor\Pages\Actions\Backups\DownloadEnvFileAction;
use ArabicSooq\FilamentEnvEditor\Pages\Actions\Backups\MakeBackupAction;
use ArabicSooq\FilamentEnvEditor\Pages\Actions\Backups\RestoreBackupAction;
use ArabicSooq\FilamentEnvEditor\Pages\Actions\Backups\ShowBackupContentAction;
use ArabicSooq\FilamentEnvEditor\Pages\Actions\Backups\UploadBackupAction;
use ArabicSooq\FilamentEnvEditor\Pages\Actions\CreateAction;
use ArabicSooq\FilamentEnvEditor\Pages\Actions\DeleteAction;
use ArabicSooq\FilamentEnvEditor\Pages\Actions\EditAction;
use ArabicSooq\FilamentEnvEditor\Pages\Actions\OptimizeClearAction;
use ArabicSooq\FilamentEnvEditor\Security\EnvSecurityChecker;
use ArabicSooq\FilamentEnvEditor\Security\SecurityFinding;
use ArabicSooq\FilamentEnvEditor\Support\EnvEntryStyler;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Concerns\InteractsWithHeaderActions;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use GeoSot\EnvEditor\Dto\BackupObj;
use GeoSot\EnvEditor\Dto\EntryObj;
use GeoSot\EnvEditor\Facades\EnvEditor;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class ViewEnv extends Page
{
    use HasUnsavedDataChangesAlert;
    use InteractsWithFormActions;
    use InteractsWithHeaderActions;

    protected string $view = 'filament-env-editor::view-editor';

    /**
     * @var list<mixed>
     */
    public array $data = [];

    protected function getHeaderActions(): array
    {
        return [
            OptimizeClearAction::make('optimize-clear'),
        ];
    }

    public function form(Schema $schema): Schema
    {
        $tabs = Tabs::make('Tabs')
            ->tabs([
                Tab::make(__('filament-env-editor::filament-env-editor.tabs.current-env.title'))
                    ->schema(fn () => $this->getFirstTab()),
                Tab::make(__('filament-env-editor::filament-env-editor.tabs.security.title'))
                    ->schema(fn () => $this->getSecurityTab())
                    ->hidden(fn (): bool => !FilamentEnvEditorPlugin::get()->isSecurityScanEnabled()),
                Tab::make(__('filament-env-editor::filament-env-editor.tabs.backups.title'))
                    ->schema(fn () => $this->getSecondTab()),
            ]);

        return $schema
            ->components([$tabs]);
    }

    public function triggerRefresh(): void
    {
        $this->dispatch('$refresh');
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentEnvEditorPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentEnvEditorPlugin::get()->getNavigationSort();
    }

    public static function getNavigationIcon(): string
    {
        return FilamentEnvEditorPlugin::get()->getNavigationIcon();
    }

    public static function getNavigationLabel(): string
    {
        return FilamentEnvEditorPlugin::get()->getNavigationLabel();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return FilamentEnvEditorPlugin::get()->getSlug();
    }

    public function getTitle(): string
    {
        return __('filament-env-editor::filament-env-editor.page.title');
    }

    public static function canAccess(): bool
    {
        return FilamentEnvEditorPlugin::get()->isAuthorized();
    }

    /**
     * @return list<Component>
     */
    private function getFirstTab(): array
    {
        $revealKeys = FilamentEnvEditorPlugin::get()->getRevealKeys();

        $envData = EnvEditor::getEnvFileContent()
            ->filter(fn (EntryObj $obj) => !$obj->isSeparator())
            ->groupBy('group')
            ->map(function (Collection $group) use ($revealKeys) {
                $fields = $group
                    ->reject(fn (EntryObj $obj) => $this->shouldHideEnvVariable($obj->key))
                    ->map(function (EntryObj $obj) use ($revealKeys) {
                        $key = $obj->key;
                        $color = EnvEntryStyler::colorForKey($key);
                        $value = EnvEntryStyler::displayValue($obj, $revealKeys);

                        $row = Group::make([
                            TextEntry::make($key)
                                ->hiddenLabel()
                                ->badge()
                                ->color($color)
                                ->state(new HtmlString("<code class='fi-env-key'>{$key}</code>"))
                                ->columnSpan(1),
                            TextEntry::make("value_{$key}")
                                ->hiddenLabel()
                                ->state(new HtmlString(
                                    "<code class='fi-env-line'>{$value}</code>"
                                ))
                                ->columnSpan(3),
                            Actions::make([
                                EditAction::make("edit_{$key}")->setEntry($obj),
                                DeleteAction::make("delete_{$key}")->setEntry($obj),
                            ])->alignEnd()->columnSpan(1),
                        ])->columns(5)->extraAttributes([
                            'class' => 'fi-env-row',
                        ]);

                        return $row;
                    })
                    ->values();

                if ($fields->isEmpty()) {
                    return null;
                }

                return Section::make()
                    ->schema($fields->all())
                    ->columns(1);
            })
            ->filter()
            ->values()
            ->all();

        $header = Group::make([
            Actions::make([
                CreateAction::make('Add'),
            ])->alignEnd(),
        ]);

        return [$header, ...$envData];
    }

    private function shouldHideEnvVariable(string $key): bool
    {
        return in_array($key, FilamentEnvEditorPlugin::get()->getHiddenKeys());
    }

    /**
     * @return list<Component>
     */
    private function getSecondTab(): array
    {
        $data = EnvEditor::getAllBackUps()
            ->map(function (BackupObj $obj, int $index) {
                return Group::make([
                    Actions::make([
                        DeleteBackupAction::make("delete_{$obj->name}")->setEntry($obj),
                        DownloadEnvFileAction::make("download_{$obj->name}")->setEntry($obj->name)->hiddenLabel()->size(Size::Small),
                        RestoreBackupAction::make("restore_{$obj->name}")->setEntry($obj->name),
                        ShowBackupContentAction::make("show_raw_content_{$obj->name}")->setEntry($obj),
                    ])->alignEnd(),
                    TextEntry::make("{$obj->name}-{$index}")
                        ->hiddenLabel()
                        ->state(new HtmlString("<strong>{$obj->name}</strong>"))
                        ->columnSpan(2),
                    TextEntry::make("created_at-{$index}")
                        ->hiddenLabel()
                        ->state($obj->createdAt->format('Y-m-d H:i:s'))
                        ->columnSpan(2),
                ])->columns(5);
            })->all();

        $header = Group::make([
            Actions::make([
                DownloadEnvFileAction::make('download_current')->tooltip('')->outlined(false),
                UploadBackupAction::make('upload'),
                MakeBackupAction::make('backup'),
            ])->alignEnd(),
        ]);

        return [$header, ...$data];
    }

    /**
     * @return list<Component>
     */
    private function getSecurityTab(): array
    {
        $checker = app(EnvSecurityChecker::class);
        $findings = $checker->scan();
        $score = $checker->score();

        $scoreColor = match (true) {
            $score >= 80 => 'success',
            $score >= 50 => 'warning',
            default => 'danger',
        };

        $summary = Group::make([
            TextEntry::make('security_score')
                ->hiddenLabel()
                ->badge()
                ->color($scoreColor)
                ->state(new HtmlString(
                    __('filament-env-editor::filament-env-editor.security.score', ['score' => $score])
                )),
            TextEntry::make('security_summary')
                ->hiddenLabel()
                ->state($this->scoreDescription($score))
                ->color($scoreColor),
        ])->columns(2)->extraAttributes(['class' => 'fi-env-security-score']);

        $rows = $findings
            ->map(function (SecurityFinding $finding): Group {
                return Group::make([
                    TextEntry::make("finding_{$finding->key}_{$finding->level->value}")
                        ->hiddenLabel()
                        ->state($finding->key)
                        ->badge()
                        ->color(EnvEntryStyler::colorForKey($finding->key))
                        ->columnSpan(1),
                    TextEntry::make("finding_msg_{$finding->key}_{$finding->level->value}")
                        ->hiddenLabel()
                        ->state($finding->message)
                        ->columnSpan(3),
                    TextEntry::make("finding_level_{$finding->key}_{$finding->level->value}")
                        ->hiddenLabel()
                        ->state($finding->level->label())
                        ->badge()
                        ->color($finding->level->color())
                        ->columnSpan(1),
                ])->columns(5);
            })
            ->all();

        $empty = Group::make([
            TextEntry::make('security_ok')
                ->hiddenLabel()
                ->badge()
                ->color('success')
                ->state(__('filament-env-editor::filament-env-editor.security.ok')),
        ]);

        return [$summary, ...(collect($rows)->isEmpty() ? [$empty] : $rows)];
    }

    private function scoreDescription(int $score): string
    {
        return match (true) {
            $score >= 80 => __('filament-env-editor::filament-env-editor.security.status.good'),
            $score >= 50 => __('filament-env-editor::filament-env-editor.security.status.attention'),
            default => __('filament-env-editor::filament-env-editor.security.status.critical'),
        };
    }
}

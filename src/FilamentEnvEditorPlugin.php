<?php

namespace ArabicSooq\FilamentEnvEditor;

use ArabicSooq\FilamentEnvEditor\Pages\ViewEnv;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentEnvEditorPlugin implements Plugin
{
    use EvaluatesClosures;

    protected bool|\Closure $authorizeUsing = true;

    protected string $viewPage = ViewEnv::class;

    protected string|\Closure|null $navigationGroup = null;

    protected int|\Closure $navigationSort = 1;

    protected string|\Closure $navigationIcon = 'heroicon-o-document-text';

    protected string|\Closure|null $navigationLabel = null;

    protected string|\Closure $slug = 'env-editor';

    /**
     * @var list<string>
     */
    protected array $hideKeys = [];

    /**
     * Keys whose (sensitive) values are allowed to be shown unmasked.
     *
     * @var list<string>
     */
    protected array $revealKeys = [];

    /**
     * Extra keys (or wildcard prefixes like "AWS_*") that must not be deleted.
     *
     * @var list<string>
     */
    protected array $protectKeys = [];

    protected bool|\Closure $securityScanEnabled = true;

    public function getId(): string
    {
        return 'filament-env-editor';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return static::make();
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                $this->viewPage,
            ]);
    }

    public function boot(Panel $panel): void
    {
    }

    public function authorize(bool|\Closure $callback = true): static
    {
        $this->authorizeUsing = $callback;

        return $this;
    }

    public function isAuthorized(): bool
    {
        return true === $this->evaluate($this->authorizeUsing);
    }

    /**
     * @param class-string<ViewEnv> $page
     */
    public function viewPage(string $page): static
    {
        $this->viewPage = $page;

        return $this;
    }

    /**
     * @return class-string<ViewEnv>
     */
    public function getViewPage(): string
    {
        return $this->viewPage;
    }

    public function navigationGroup(string|\Closure|null $navigationGroup): static
    {
        $this->navigationGroup = $navigationGroup;

        return $this;
    }

    public function getNavigationGroup(): string
    {
        return $this->evaluate($this->navigationGroup) ?? __('filament-env-editor::filament-env-editor.navigation.group');
    }

    public function navigationSort(int|\Closure $navigationSort): static
    {
        $this->navigationSort = $navigationSort;

        return $this;
    }

    public function getNavigationSort(): int
    {
        return $this->evaluate($this->navigationSort);
    }

    public function navigationIcon(string|\Closure $navigationIcon): static
    {
        $this->navigationIcon = $navigationIcon;

        return $this;
    }

    public function getNavigationIcon(): string
    {
        return $this->evaluate($this->navigationIcon);
    }

    public function navigationLabel(string|\Closure|null $navigationLabel): static
    {
        $this->navigationLabel = $navigationLabel;

        return $this;
    }

    public function getNavigationLabel(): string
    {
        return $this->evaluate($this->navigationLabel) ?? __('filament-env-editor::filament-env-editor.navigation.label');
    }

    public function slug(string|\Closure $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->evaluate($this->slug);
    }

    public function hideKeys(string ...$keys): static
    {
        $this->hideKeys = $keys;

        return $this;
    }

    /**
     * Retrieves the list of environment keys that should be hidden from the UI.
     *
     * @return list<string>
     */
    public function getHiddenKeys(): array
    {
        return $this->hideKeys;
    }

    public function revealKeys(string ...$keys): static
    {
        $this->revealKeys = $keys;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getRevealKeys(): array
    {
        return $this->revealKeys;
    }

    public function protectKeys(string ...$keys): static
    {
        $this->protectKeys = $keys;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getProtectKeys(): array
    {
        return $this->protectKeys;
    }

    public function securityScan(bool|\Closure $enabled = true): static
    {
        $this->securityScanEnabled = $enabled;

        return $this;
    }

    public function isSecurityScanEnabled(): bool
    {
        return (bool) $this->evaluate($this->securityScanEnabled);
    }
}

<?php

declare(strict_types=1);

namespace MobikulAppBar;

final class HtmlAppBar
{
    /** @var array<string, string> */
    private const ICON_SVGS = [
        'back' => '<path d="M15 18l-6-6 6-6"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/>',
        'cart' => '<path d="M4 5h2l2.2 9.2a1 1 0 0 0 1 .8h7.6a1 1 0 0 0 1-.8L19 8H7"/><circle cx="10" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/>',
        'profile' => '<circle cx="12" cy="9" r="3.5"/><path d="M5 20c1.7-3.3 4.1-5 7-5s5.3 1.7 7 5"/>',
        'wishlist' => '<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.5A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/>',
        'share' => '<circle cx="18" cy="5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="19" r="2.5"/><path d="M8.2 11l7.4-4.3M8.2 13l7.4 4.3"/>',
        'filter' => '<path d="M4 6h16M7 12h10M10 18h4"/>',
        'notifications' => '<path d="M9 19a3 3 0 0 0 6 0M6.5 16h11l-1.1-1.8a2.3 2.3 0 0 1-.4-1.3V10a4 4 0 1 0-8 0v2.9c0 .5-.1.9-.4 1.3z"/>',
        'more' => '<circle cx="5" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="19" cy="12" r="1.8"/>',
    ];

    private string $title;

    /** @var array<int, array{icon:string, action:string}> */
    private array $icons;

    /** @var array<string, mixed> */
    private array $options;

    public function __construct(string $title = 'App', array $icons = [], array $options = [])
    {
        $this->title = $title;
        $this->icons = $icons;
        $this->options = $this->normalizeOptions($options);
    }

    public function render(string $id = 'mobikul-native-app-bar'): string
    {
        $safeId = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
        $safeTitle = htmlspecialchars($this->title, ENT_QUOTES, 'UTF-8');
        $style = htmlspecialchars($this->buildInlineStyle(), ENT_QUOTES, 'UTF-8');

        $leading = $this->options['isLeadingEnable']
            ? $this->renderIconButton('back', 'go_back', 'mobikul-app-bar-leading')
            : '';

        $brand = $this->buildBrandMarkup();
        $actions = $this->buildActionMarkup();
        $elevatedClass = $this->options['isElevated'] ? ' mobikul-app-bar-elevated' : '';

        return <<<HTML
<header id="{$safeId}" class="mobikul-app-bar{$elevatedClass}" role="banner" style="{$style}">
    <div class="mobikul-app-bar-main">
        {$leading}
        {$brand}
        <div class="mobikul-app-bar-title-group">
            <div class="mobikul-app-bar-title">{$safeTitle}</div>
        </div>
    </div>
    <div class="mobikul-app-bar-actions">{$actions}</div>
</header>
HTML;
    }

    /** @param array<string, mixed> $options */
    private function normalizeOptions(array $options): array
    {
        return [
            'showAppLogo' => (bool) ($options['showAppLogo'] ?? false),
            'isElevated' => (bool) ($options['isElevated'] ?? true),
            'isLeadingEnable' => (bool) ($options['isLeadingEnable'] ?? false),
            'isHomeEnable' => (bool) ($options['isHomeEnable'] ?? false),
            'isAppLogoForDarkmode' => (bool) ($options['isAppLogoForDarkmode'] ?? false),
            'appLogoUrl' => $this->sanitizeNullableString($options['appLogoUrl'] ?? null),
            'darkAppLogoUrl' => $this->sanitizeNullableString($options['darkAppLogoUrl'] ?? null),
            'placeHolderImage' => $this->sanitizeNullableString($options['placeHolderImage'] ?? null),
            'appBarBackgroundColor' => $this->sanitizeColor($options['appBarBackgroundColor'] ?? '#ffffff'),
            'titleColor' => $this->sanitizeColor($options['titleColor'] ?? '#111827'),
            'titleFontSize' => max(12, (int) ($options['titleFontSize'] ?? 18)),
            'titleFontWeight' => preg_replace('/[^0-9a-zA-Z -]/', '', (string) ($options['titleFontWeight'] ?? '600')) ?: '600',
            'logoWidth' => max(20, (int) ($options['logoWidth'] ?? 32)),
            'logoHeight' => max(20, (int) ($options['logoHeight'] ?? 32)),
        ];
    }

    private function buildBrandMarkup(): string
    {
        if (! $this->options['showAppLogo']) {
            return '';
        }

        $brandAction = $this->options['isHomeEnable'] ? 'open_home' : 'noop';
        $brandClass = 'mobikul-app-bar-brand' . ($this->options['isHomeEnable'] ? ' mobikul-app-bar-brand-clickable' : '');
        $brandStyle = htmlspecialchars(sprintf(
            '--mobikul-logo-width:%dpx;--mobikul-logo-height:%dpx;',
            $this->options['logoWidth'],
            $this->options['logoHeight']
        ), ENT_QUOTES, 'UTF-8');

        $content = $this->buildLogoContent();

        return sprintf(
            '<button class="%s" type="button" data-action="%s" aria-label="home" style="%s">%s</button>',
            htmlspecialchars($brandClass, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($brandAction, ENT_QUOTES, 'UTF-8'),
            $brandStyle,
            $content
        );
    }

    private function buildLogoContent(): string
    {
        $logoUrl = $this->options['appLogoUrl'];
        $fallback = strtoupper(substr(trim($this->title), 0, 1) ?: 'M');

        if ($logoUrl) {
            $safeUrl = htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8');
            $placeholder = htmlspecialchars($this->options['placeHolderImage'] ?? '', ENT_QUOTES, 'UTF-8');

            return sprintf(
                '<img class="mobikul-app-bar-logo" src="%s" alt="logo" data-placeholder="%s">',
                $safeUrl,
                $placeholder
            );
        }

        return sprintf('<span class="mobikul-app-bar-logo-fallback">%s</span>', htmlspecialchars($fallback, ENT_QUOTES, 'UTF-8'));
    }

    private function buildActionMarkup(): string
    {
        $actions = '';

        foreach ($this->icons as $icon) {
            $actions .= $this->renderIconButton(
                (string) ($icon['icon'] ?? 'menu'),
                (string) ($icon['action'] ?? 'noop')
            );
        }

        return $actions;
    }

    private function renderIconButton(string $iconName, string $action, string $extraClass = ''): string
    {
        $safeIconName = htmlspecialchars($iconName, ENT_QUOTES, 'UTF-8');
        $safeAction = htmlspecialchars($action, ENT_QUOTES, 'UTF-8');
        $iconMarkup = $this->buildIconSvg($iconName);
        $className = trim('mobikul-app-bar-btn mobikul-app-bar-icon-btn ' . $extraClass);
        $safeClassName = htmlspecialchars($className, ENT_QUOTES, 'UTF-8');

        return sprintf(
            '<button class="%s" type="button" data-action="%s" aria-label="%s">%s</button>',
            $safeClassName,
            $safeAction,
            $safeIconName,
            $iconMarkup
        );
    }

    private function buildIconSvg(string $iconName): string
    {
        $path = self::ICON_SVGS[$iconName] ?? self::ICON_SVGS['menu'];

        return sprintf(
            '<svg class="mobikul-app-bar-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">%s</svg>',
            $path
        );
    }

    private function buildInlineStyle(): string
    {
        return sprintf(
            '--mobikul-app-bar-bg:%s;--mobikul-app-bar-title:%s;--mobikul-app-bar-title-size:%dpx;--mobikul-app-bar-title-weight:%s;',
            $this->options['appBarBackgroundColor'],
            $this->options['titleColor'],
            $this->options['titleFontSize'],
            $this->options['titleFontWeight']
        );
    }

    private function sanitizeNullableString(mixed $value): ?string
    {
        $string = is_string($value) ? trim($value) : '';

        return $string !== '' ? $string : null;
    }

    private function sanitizeColor(mixed $value): string
    {
        $color = is_string($value) ? trim($value) : '';

        if ($color === '') {
            return '#ffffff';
        }

        if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $color) === 1) {
            return $color;
        }

        if (preg_match('/^(rgb|rgba|hsl|hsla)\([^)]+\)$/', $color) === 1) {
            return $color;
        }

        return '#ffffff';
    }
}

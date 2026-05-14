<?php

declare(strict_types=1);

namespace MobikulAppBar;

use MobikulAppBar\ValueObjects\AppBarCustomValues;
use MobikulAppBar\ValueObjects\AppBarIcon;

final class MobikulAppBarPlugin
{
    public const VERSION = '1.0.0';
    public const CONFIGURE_METHOD = 'MobikulAppBar.Configure';
    public const RESET_METHOD = 'MobikulAppBar.Reset';

    /** @param array<string,mixed> $defaultOptions */
    public function __construct(
        private readonly array $defaultOptions = []
    ) {
    }

    /** @param array<int, array{icon:string,action:string}> $icons */
    public function configure(
        string $title,
        array $icons = [],
        string $context = 'default',
        array $options = []
    ): array
    {
        return [
            'method' => self::CONFIGURE_METHOD,
            'params' => [
                'title' => $title,
                'icons' => $icons,
                'context' => $context,
                'options' => array_merge($this->defaultOptions, $options),
            ],
        ];
    }

    /**
     * @param AppBarIcon[] $icons
     */
    public function configureWithCustomValues(
        string $title,
        AppBarCustomValues $customValues,
        array $icons = [],
        string $context = 'default'
    ): array {
        return $this->configure(
            title: $title,
            icons: array_map(static fn (AppBarIcon $icon): array => $icon->toArray(), $icons),
            context: $context,
            options: $customValues->toArray()
        );
    }

    public function reset(): array
    {
        return [
            'method' => self::RESET_METHOD,
            'params' => [],
        ];
    }

    /** @param array<int, array{icon:string,action:string}> $icons */
    public function renderHtml(
        string $title,
        array $icons = [],
        array $options = [],
        string $id = 'mobikul-native-app-bar'
    ): string {
        $resolvedOptions = array_merge($this->defaultOptions, $options);

        return (new HtmlAppBar(
            title: $title,
            icons: $icons,
            options: $resolvedOptions
        ))->render($id);
    }

    /**
     * @param AppBarIcon[] $icons
     */
    public function renderHtmlWithCustomValues(
        string $title,
        AppBarCustomValues $customValues,
        array $icons = [],
        string $id = 'mobikul-native-app-bar'
    ): string {
        return $this->renderHtml(
            title: $title,
            icons: array_map(static fn (AppBarIcon $icon): array => $icon->toArray(), $icons),
            options: $customValues->toArray(),
            id: $id
        );
    }
}

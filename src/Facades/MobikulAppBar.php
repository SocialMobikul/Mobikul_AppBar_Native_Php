<?php

declare(strict_types=1);

namespace MobikulAppBar\Facades;

use Illuminate\Support\Facades\Facade;
use MobikulAppBar\ValueObjects\AppBarCustomValues;
use MobikulAppBar\ValueObjects\AppBarIcon;

/**
 * @method static array configure(string $title, array $icons = [], string $context = 'default', array $options = [])
 * @method static array configureWithCustomValues(string $title, AppBarCustomValues $customValues, array $icons = [], string $context = 'default')
 * @method static array reset()
 * @method static string renderHtml(string $title, array $icons = [], array $options = [], string $id = 'mobikul-native-app-bar')
 * @method static string renderHtmlWithCustomValues(string $title, AppBarCustomValues $customValues, array $icons = [], string $id = 'mobikul-native-app-bar')
 *
 * @see \MobikulAppBar\MobikulAppBarPlugin
 */
class MobikulAppBar extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'mobikul-app-bar';
    }
}

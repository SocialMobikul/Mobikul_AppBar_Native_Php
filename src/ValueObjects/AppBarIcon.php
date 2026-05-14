<?php

declare(strict_types=1);

namespace MobikulAppBar\ValueObjects;

use InvalidArgumentException;

final class AppBarIcon
{
    /** @var string[] */
    private const ALLOWED_ICONS = [
        'back',
        'menu',
        'search',
        'cart',
        'profile',
        'wishlist',
        'share',
        'filter',
        'notifications',
        'more',
    ];

    public function __construct(
        private readonly string $icon,
        private readonly string $action
    ) {
        if (! in_array($this->icon, self::ALLOWED_ICONS, true)) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported icon "%s". Allowed icons: %s',
                $this->icon,
                implode(', ', self::ALLOWED_ICONS)
            ));
        }

        if (trim($this->action) === '') {
            throw new InvalidArgumentException('Icon action must not be empty.');
        }
    }

    /** @return array{icon:string,action:string} */
    public function toArray(): array
    {
        return [
            'icon' => $this->icon,
            'action' => $this->action,
        ];
    }
}

<?php

declare(strict_types=1);

namespace MobikulAppBar\ValueObjects;

final class AppBarCustomValues
{
    /** @param array<string,mixed> $values */
    private function __construct(private array $values)
    {
    }

    /** @param array<string,mixed> $defaults */
    public static function make(array $defaults = []): self
    {
        return new self(array_merge([
            'showAppLogo' => false,
            'isElevated' => true,
            'isLeadingEnable' => false,
            'isHomeEnable' => false,
            'isAppLogoForDarkmode' => false,
            'appLogoUrl' => null,
            'darkAppLogoUrl' => null,
            'placeHolderImage' => null,
            'appBarBackgroundColor' => '#ffffff',
            'titleColor' => '#111827',
            'titleFontSize' => 18,
            'titleFontWeight' => '600',
            'logoWidth' => 32,
            'logoHeight' => 32,
        ], $defaults));
    }

    public function withLogo(
        string $appLogoUrl,
        ?string $darkAppLogoUrl = null,
        ?string $placeholderImage = null
    ): self {
        $clone = clone $this;
        $clone->values['showAppLogo'] = true;
        $clone->values['appLogoUrl'] = $appLogoUrl;
        $clone->values['darkAppLogoUrl'] = $darkAppLogoUrl;
        $clone->values['placeHolderImage'] = $placeholderImage;

        return $clone;
    }

    public function withLeading(bool $enabled = true): self
    {
        $clone = clone $this;
        $clone->values['isLeadingEnable'] = $enabled;

        return $clone;
    }

    public function withHomeTap(bool $enabled = true): self
    {
        $clone = clone $this;
        $clone->values['isHomeEnable'] = $enabled;

        return $clone;
    }

    public function withElevation(bool $enabled = true): self
    {
        $clone = clone $this;
        $clone->values['isElevated'] = $enabled;

        return $clone;
    }

    public function withDarkModeLogo(bool $enabled = true): self
    {
        $clone = clone $this;
        $clone->values['isAppLogoForDarkmode'] = $enabled;

        return $clone;
    }

    public function withTitleStyle(string $color, int $fontSize = 18, string $fontWeight = '600'): self
    {
        $clone = clone $this;
        $clone->values['titleColor'] = $color;
        $clone->values['titleFontSize'] = max(12, $fontSize);
        $clone->values['titleFontWeight'] = $fontWeight;

        return $clone;
    }

    public function withBackground(string $color): self
    {
        $clone = clone $this;
        $clone->values['appBarBackgroundColor'] = $color;

        return $clone;
    }

    public function withLogoSize(int $width, int $height): self
    {
        $clone = clone $this;
        $clone->values['logoWidth'] = max(20, $width);
        $clone->values['logoHeight'] = max(20, $height);

        return $clone;
    }

    /** @param array<string,mixed> $overrides */
    public function withOverrides(array $overrides): self
    {
        $clone = clone $this;
        $clone->values = array_merge($clone->values, $overrides);

        return $clone;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return $this->values;
    }
}

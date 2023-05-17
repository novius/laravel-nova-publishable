<?php

namespace Novius\LaravelNovaPublishable\Nova\Fields;

use Laravel\Nova\Fields\Badge;
use Novius\LaravelPublishable\Traits\Publishable;

class PublicationBadge extends Badge
{
    public function __construct($name, callable $resolveCallback = null)
    {
        parent::__construct($name, function () {
            /** @var Publishable $this */
            if ($this->isPublished()) {
                return 'success';
            }
            if ($this->willBePublished()) {
                return 'warning';
            }

            return 'danger';
        }, $resolveCallback);

        $this->icons([
            'danger' => 'ban',
            'warning' => 'clock',
            'success' => 'check',
        ])
            ->label(function () {
                return $this->resource->publicationLabel();
            });
    }

    public static function make(mixed $name, ?callable $resolveCallback = null): PublicationBadge|static
    {
        return new static($name, $resolveCallback);
    }
}

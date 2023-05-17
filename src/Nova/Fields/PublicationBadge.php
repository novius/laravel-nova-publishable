<?php

namespace Novius\LaravelNovaPublishable\Nova\Fields;

use Laravel\Nova\Fields\Badge;
use Novius\LaravelPublishable\Traits\Publishable;

class PublicationBadge extends Badge
{
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
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
}

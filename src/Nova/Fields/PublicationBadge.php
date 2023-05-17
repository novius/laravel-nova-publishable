<?php

namespace Novius\LaravelNovaPublishable\Nova\Fields;

use Laravel\Nova\Fields\Badge;

class PublicationBadge extends Badge
{
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, function () {
            if ($this->resource->isPublished()) {
                return 'success';
            }
            if ($this->resource->willBePublished()) {
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

<?php

namespace Novius\LaravelNovaPublishable\Nova\Fields;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelPublishable\Traits\Publishable;
use RuntimeException;

/**
 * @method static static make(mixed $name = null, string|\Closure|callable|object|null $attribute = null, callable|null $resolveCallback = null)
 */
class PublicationBadge extends Badge
{
    public function __construct($name = null, $attribute = null, callable $resolveCallback = null)
    {
        $request = app()->get(NovaRequest::class);
        $resource = $request->newResource();
        /** @var Publishable&Model $model */
        $model = $resource->model();
        if (! in_array(Publishable::class, class_uses_recursive($model))) {
            throw new RuntimeException('Resource must use trait Novius\LaravePublishable\Traits\Publishable');
        }
        $name = $name ?? trans('laravel-nova-publishable::messages.fields.publication_status');

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

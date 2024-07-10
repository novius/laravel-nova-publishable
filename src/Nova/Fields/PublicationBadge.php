<?php

namespace Novius\LaravelNovaPublishable\Nova\Fields;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelPublishable\Traits\Publishable;

/**
 * @method static static make(mixed $name = null, string|\Closure|callable|object|null $attribute = null, callable|null $resolveCallback = null)
 */
class PublicationBadge extends Badge
{
    public function __construct($name = null, $attribute = null, ?callable $resolveCallback = null)
    {
        $name = $name ?? trans('laravel-nova-publishable::messages.fields.publication_status');

        $request = app()->get(NovaRequest::class);
        $resource = $request->newResource();
        /** @var Publishable&Model $model */
        $model = $resource->model();

        $is_publishable = in_array(Publishable::class, class_uses_recursive($model));

        parent::__construct($name, function () use ($is_publishable) {
            if ($is_publishable) {
                if ($this->resource->isPublished()) {
                    return 'success';
                }
                if ($this->resource->willBePublished()) {
                    return 'warning';
                }
            }

            return 'danger';
        }, $resolveCallback);

        $this
            ->icons([
                'danger' => 'ban',
                'warning' => 'clock',
                'success' => 'check',
            ])
            ->label(function () use ($is_publishable) {
                return $is_publishable ? $this->resource->publicationLabel() : '';
            });
    }
}

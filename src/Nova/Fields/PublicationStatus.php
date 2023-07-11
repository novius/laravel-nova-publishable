<?php

namespace Novius\LaravelNovaPublishable\Nova\Fields;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelPublishable\Enums\PublicationStatus as PublicationStatusEnum;
use Novius\LaravelPublishable\Traits\Publishable;
use RuntimeException;

/**
 * @method static static make(mixed $name = null, string|\Closure|callable|object|null $attribute = null, callable|null $resolveCallback = null)
 */
class PublicationStatus extends Select
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
        $attribute = $attribute ?? $model->getPublicationStatusColumn();

        parent::__construct($name, $attribute, $resolveCallback);

        $this->options(function () use ($model) {
            $statuses = [
                PublicationStatusEnum::draft->value => PublicationStatusEnum::draft->getLabel(),
                PublicationStatusEnum::published->value => PublicationStatusEnum::published->getLabel(),
                PublicationStatusEnum::unpublished->value => PublicationStatusEnum::unpublished->getLabel(),
                PublicationStatusEnum::scheduled->value => PublicationStatusEnum::scheduled->getLabel(),
            ];

            if ($this->resource->{$model->getPublishedFirstAtColumn()} !== null) {
                unset($statuses[PublicationStatusEnum::draft->value]);
            } else {
                unset($statuses[PublicationStatusEnum::unpublished->value]);
            }

            return $statuses;
        })
            ->rules('required')
            ->displayUsingLabels();
    }
}

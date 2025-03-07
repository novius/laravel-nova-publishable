<?php

namespace Novius\LaravelNovaPublishable\Nova\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelPublishable\Enums\PublicationStatus as PublicationStatusEnum;
use Novius\LaravelPublishable\Traits\Publishable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @method static static make(mixed $name = null, string|Closure|callable|object|null $attribute = null, callable|null $resolveCallback = null)
 */
class PublicationStatus extends Select
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct($name = null, $attribute = null, ?callable $resolveCallback = null)
    {
        $name = $name ?? trans('laravel-nova-publishable::messages.fields.publication_status');

        $request = app()->get(NovaRequest::class);
        $resource = $request->newResource();
        /** @var Publishable&Model $model */
        $model = $resource->model();

        $is_publishable = in_array(Publishable::class, class_uses_recursive($model), true);
        if ($is_publishable) {
            $attribute = $attribute ?? $model->getPublicationStatusColumn();
        }

        parent::__construct($name, $attribute, $resolveCallback);

        $this->rules('required')
            ->displayUsingLabels()
            ->options(function () use ($model, $is_publishable) {
                $statuses = [
                    PublicationStatusEnum::draft->value => PublicationStatusEnum::draft->getLabel(),
                    PublicationStatusEnum::published->value => PublicationStatusEnum::published->getLabel(),
                    PublicationStatusEnum::unpublished->value => PublicationStatusEnum::unpublished->getLabel(),
                    PublicationStatusEnum::scheduled->value => PublicationStatusEnum::scheduled->getLabel(),
                ];

                if ($is_publishable) {
                    if ($this->resource->{$model->getPublishedFirstAtColumn()} !== null) {
                        unset($statuses[PublicationStatusEnum::draft->value]);
                    } else {
                        unset($statuses[PublicationStatusEnum::unpublished->value]);
                    }
                }

                return $statuses;
            });
    }
}

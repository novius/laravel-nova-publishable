<?php

namespace Novius\LaravelNovaPublishable\Nova\Fields;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelPublishable\Enums\PublicationStatus;
use Novius\LaravelPublishable\Traits\Publishable;
use RuntimeException;

/**
 * @method static static make(mixed $name = null, string|\Closure|callable|object|null $attribute = null, callable|null $resolveCallback = null)
 */
class PublishedFirstAt extends DateTime
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
        $name = $name ?? trans('laravel-nova-publishable::messages.fields.published_first_at');
        $attribute = $attribute ?? $model->getPublishedFirstAtColumn();

        parent::__construct($name, $attribute, $resolveCallback);

        $this->nullable()
            ->rules('nullable', 'date')
            ->hideWhenCreating()
            ->hideWhenUpdating(function (NovaRequest $request, Model $model) {
                return ! $model->{$model->getPublishedFirstAtColumn()};
            })
            ->hideFromDetail(function (NovaRequest $request, Model $model) {
                return ! $model->isPublished();
            })
            ->dependsOn(
                [$model->getPublicationStatusColumn()],
                function (DateTime $field, NovaRequest $request, FormData $formData) use ($model) {
                    if (in_array($formData->{$model->getPublicationStatusColumn()}, [PublicationStatus::draft->value, PublicationStatus::unpublished->value], true)) {
                        $field->hide();
                    } else {
                        $field->show();
                    }
                }
            );
    }
}

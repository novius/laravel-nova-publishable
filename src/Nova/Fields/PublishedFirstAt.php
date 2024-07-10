<?php

namespace Novius\LaravelNovaPublishable\Nova\Fields;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelPublishable\Enums\PublicationStatus;
use Novius\LaravelPublishable\Traits\Publishable;

/**
 * @method static static make(mixed $name = null, string|\Closure|callable|object|null $attribute = null, callable|null $resolveCallback = null)
 */
class PublishedFirstAt extends DateTime
{
    public function __construct($name = null, $attribute = null, ?callable $resolveCallback = null)
    {
        $name = $name ?? trans('laravel-nova-publishable::messages.fields.published_first_at');

        $request = app()->get(NovaRequest::class);
        $resource = $request->newResource();
        /** @var Publishable&Model $model */
        $model = $resource->model();

        $is_publishable = in_array(Publishable::class, class_uses_recursive($model));
        if ($is_publishable) {
            $attribute = $attribute ?? $model->getPublishedFirstAtColumn();
        }

        parent::__construct($name, $attribute, $resolveCallback);

        $this->nullable()
            ->rules('nullable', 'date')
            ->hideWhenCreating()
            ->hideWhenUpdating(function (NovaRequest $request, Model $model) use ($is_publishable) {
                if ($is_publishable) {
                    return ! $model->{$model->getPublishedFirstAtColumn()};
                }

                return false;
            })
            ->hideFromDetail(function (NovaRequest $request, Model $model) use ($is_publishable) {
                if ($is_publishable) {
                    return ! $model->isPublished();
                }

                return false;
            });

        if ($is_publishable) {
            $this->dependsOn(
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
}

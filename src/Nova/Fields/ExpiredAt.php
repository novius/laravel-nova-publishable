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
class ExpiredAt extends DateTime
{
    public function __construct($name = null, $attribute = null, callable $resolveCallback = null)
    {
        $name = $name ?? trans('laravel-nova-publishable::messages.fields.expired_at');

        $request = app()->get(NovaRequest::class);
        $resource = $request->newResource();
        /** @var Publishable&Model $model */
        $model = $resource->model();

        $rules = ['nullable', 'date'];
        $is_publishable = in_array(Publishable::class, class_uses_recursive($model));
        if ($is_publishable) {
            $attribute = $attribute ?? $model->getExpiredAtColumn();
            $rules[] = 'after:'.$model->getPublishedAtColumn();
        }

        parent::__construct($name, $attribute, $resolveCallback);

        $this->nullable()
            ->rules($rules);

        if ($is_publishable) {
            $this->dependsOn(
                [$model->getPublicationStatusColumn()],
                function (DateTime $field, NovaRequest $request, FormData $formData) use ($model) {
                    if ($formData->{$model->getPublicationStatusColumn()} === PublicationStatus::scheduled->value) {
                        $field->show();
                    } else {
                        $formData->{$field->attribute} = null;
                        $field->hide();
                    }
                }
            );
        }
    }
}

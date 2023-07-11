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
class ExpiredAt extends DateTime
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
        $name = $name ?? trans('laravel-nova-publishable::messages.fields.expired_at');
        $attribute = $attribute ?? $model->getExpiredAtColumn();

        parent::__construct($name, $attribute, $resolveCallback);

        $this->nullable()
            ->rules('nullable', 'date', 'after:'.$model->getPublishedAtColumn())
            ->dependsOn(
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

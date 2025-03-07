<?php

namespace Novius\LaravelNovaPublishable\Nova\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelPublishable\Enums\PublicationStatus;
use Novius\LaravelPublishable\Traits\Publishable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @method static static make(mixed $name = null, string|Closure|callable|object|null $attribute = null, callable|null $resolveCallback = null)
 */
class PublishedAt extends DateTime
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct($name = null, $attribute = null, ?callable $resolveCallback = null)
    {
        $name = $name ?? trans('laravel-nova-publishable::messages.fields.published_at');

        $request = app()->get(NovaRequest::class);
        $resource = $request->newResource();
        /** @var Publishable&Model $model */
        $model = $resource->model();

        $is_publishable = in_array(Publishable::class, class_uses_recursive($model), true);
        if ($is_publishable) {
            $attribute = $attribute ?? $model->getPublishedAtColumn();
        }

        parent::__construct($name, $attribute, $resolveCallback);

        $this->nullable()
            ->rules('nullable', 'date');

        if ($is_publishable) {
            $this->dependsOn(
                [$model->getPublicationStatusColumn()],
                function (DateTime $field, NovaRequest $request, FormData $formData) use ($model) {
                    if ($formData->{$model->getPublicationStatusColumn()} === PublicationStatus::scheduled->value) {
                        $field->show()->rules(['required', 'date']);
                    } else {
                        $formData->{$field->attribute} = null;
                        $field->hide()->rules('nullable', 'date');
                    }
                }
            );
        }
    }
}

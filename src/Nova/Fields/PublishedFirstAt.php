<?php

namespace Novius\LaravelNovaPublishable\Nova\Fields;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelPublishable\Enums\PublicationStatus;

class PublishedFirstAt extends DateTime
{
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->nullable()
            ->rules('nullable', 'date')
            ->hideWhenCreating()
            ->hideWhenUpdating(function (NovaRequest $request, Model $model) {
                return ! $model->{$this->resource->getPublishedFirstAtColumn()};
            });
    }

    public function dependsOnPublicationStatus(string $publication_status_column): PublishedFirstAt
    {
        return $this->dependsOn(
            [$publication_status_column],
            function (DateTime $field, NovaRequest $request, FormData $formData) use ($publication_status_column) {
                if ($formData->{$publication_status_column} === PublicationStatus::draft) {
                    $field->hide();
                } else {
                    $field->show()->rules(['required', 'date']);
                }
            }
        );
    }
}

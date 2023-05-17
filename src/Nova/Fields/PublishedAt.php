<?php

namespace Novius\LaravelNovaPublishable\Nova\Fields;

use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelPublishable\Enums\PublicationStatus;

class PublishedAt extends DateTime
{
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->nullable()
            ->rules('nullable', 'date');
    }

    public function dependsOnPublicationStatus(string $publication_status_column): PublishedAt
    {
        return $this->dependsOn(
            [$publication_status_column],
            function (DateTime $field, NovaRequest $request, FormData $formData) use ($publication_status_column) {
                if ($formData->{$publication_status_column} === PublicationStatus::scheduled->value) {
                    $field->show()->rules(['required', 'date']);
                } else {
                    $formData->{$publication_status_column} = null;
                    $field->hide()->rules('nullable', 'date');
                }
            }
        );
    }
}

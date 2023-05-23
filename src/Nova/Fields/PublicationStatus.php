<?php

namespace Novius\LaravelNovaPublishable\Nova\Fields;

use Laravel\Nova\Fields\Select;
use Novius\LaravelPublishable\Enums\PublicationStatus as PublicationStatusEnum;

class PublicationStatus extends Select
{
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->options([
            PublicationStatusEnum::draft->value => PublicationStatusEnum::draft->getLabel(),
            PublicationStatusEnum::published->value => PublicationStatusEnum::published->getLabel(),
            PublicationStatusEnum::unpublished->value => PublicationStatusEnum::unpublished->getLabel(),
            PublicationStatusEnum::scheduled->value => PublicationStatusEnum::scheduled->getLabel(),
        ])
            ->rules('required')
            ->displayUsingLabels();
    }

    public function optionsDependsOnPublishedFirstAt(string $published_first_at_column): PublicationStatus
    {
        return $this->options(function () use ($published_first_at_column) {
            $status = [
                PublicationStatusEnum::draft->value => PublicationStatusEnum::draft->getLabel(),
                PublicationStatusEnum::published->value => PublicationStatusEnum::published->getLabel(),
                PublicationStatusEnum::unpublished->value => PublicationStatusEnum::unpublished->getLabel(),
                PublicationStatusEnum::scheduled->value => PublicationStatusEnum::scheduled->getLabel(),
            ];

            if ($this->resource->{$published_first_at_column} !== null) {
                unset($status[PublicationStatusEnum::draft->value]);
            } else {
                unset($status[PublicationStatusEnum::unpublished->value]);
            }
        });
    }
}

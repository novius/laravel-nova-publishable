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
            PublicationStatusEnum::scheduled->value => PublicationStatusEnum::scheduled->getLabel(),
        ])
            ->rules('required')
            ->displayUsingLabels();
    }
}

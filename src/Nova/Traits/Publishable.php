<?php

namespace Novius\LaravelNovaPublishable\Nova\Traits;

use Illuminate\Database\Eloquent\Model;
use Novius\LaravelNovaPublishable\Nova\Fields\ExpiredAt;
use Novius\LaravelNovaPublishable\Nova\Fields\PublicationBadge;
use Novius\LaravelNovaPublishable\Nova\Fields\PublicationStatus as PublicationStatusField;
use Novius\LaravelNovaPublishable\Nova\Fields\PublishedAt;
use Novius\LaravelNovaPublishable\Nova\Fields\PublishedFirstAt;

/**
 * @property Model|\Novius\LaravelPublishable\Traits\Publishable $resource
 */
trait Publishable
{
    protected function publishableFields(): array
    {
        return [
            ...$this->publishableDisplayFields(),
            ...$this->publishableFormFields(),
        ];
    }

    protected function publishableDisplayFields(): array
    {
        return [
            PublicationBadge::make(trans('laravel-nova-publishable::messages.fields.publication_status')),
        ];
    }

    protected function publishableFormFields(): array
    {
        return [
            PublicationStatusField::make(
                trans('laravel-nova-publishable::messages.fields.publication_status'),
                $this->resource->getPublicationStatusColumn()
            )
                ->optionsDependsOnPublishedFirstAt($this->resource->getPublishedFirstAtColumn())
                ->onlyOnForms(),

            PublishedFirstAt::make(trans('laravel-nova-publishable::messages.fields.published_first_at'), $this->resource->getPublishedFirstAtColumn())
                ->dependsOnPublicationStatus($this->resource->getPublicationStatusColumn())
                ->hideFromIndex()
                ->hideFromDetail(function () {
                    /** @var \Novius\LaravelPublishable\Traits\Publishable $this */
                    return ! $this->isPublished();
                }),

            PublishedAt::make(trans('laravel-nova-publishable::messages.fields.published_at'), $this->resource->getPublishedAtColumn())
                ->dependsOnPublicationStatus($this->resource->getPublicationStatusColumn())
                ->onlyOnForms(),

            ExpiredAt::make(trans('laravel-nova-publishable::messages.fields.expired_at'), $this->resource->getExpiredAtColumn())
                ->dependsOnPublicationStatus($this->resource->getPublicationStatusColumn())
                ->dependsOnPublishedAt($this->resource->getPublishedAtColumn())
                ->onlyOnForms(),
        ];
    }
}

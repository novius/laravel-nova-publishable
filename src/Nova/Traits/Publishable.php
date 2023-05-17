<?php

namespace Novius\LaravelNovaPublishable\Nova\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelPublishable\Enums\PublicationStatus;
use Novius\LaravelPublishable\Scopes\PublishableScope;

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
            Badge::make(trans('laravel-nova-publishable::fields.publication_status'), function () {
                /** @var \Novius\LaravelPublishable\Traits\Publishable $this */
                if ($this->isPublished()) {
                    return 'success';
                }
                if ($this->willBePublished()) {
                    return 'warning';
                }

                return 'danger';
            })
                ->icons([
                    'danger' => 'ban',
                    'warning' => 'clock',
                    'success' => 'check',
                ])
                ->label(function () {
                    return $this->resource->publicationLabel();
                }),
        ];
    }

    protected function publishableFormFields(): array
    {
        return [
            Select::make(trans('laravel-nova-publishable::fields.publication_status'), $this->resource->getPublicationStatusColumn())
                ->options([
                    PublicationStatus::draft->value => PublicationStatus::draft->getLabel(),
                    PublicationStatus::published->value => PublicationStatus::published->getLabel(),
                    PublicationStatus::scheduled->value => PublicationStatus::scheduled->getLabel(),
                ])
                ->displayUsingLabels()
                ->onlyOnForms(),

            DateTime::make(trans('laravel-nova-publishable::fields.published_first_at'), $this->resource->getPublishedFirstAtColumn())
                ->hideWhenCreating()
                ->hideWhenUpdating(function (NovaRequest $request, Model $article) {
                    return ! $article->{$this->resource->getPublishedFirstAtColumn()};
                })
                ->nullable()
                ->rules('nullable', 'date')
                ->dependsOn(
                    [$this->resource->getPublicationStatusColumn()],
                    function (DateTime $field, NovaRequest $request, FormData $formData) {
                        if ($formData->{$this->resource->getPublicationStatusColumn()} === PublicationStatus::draft) {
                            $field->hide();
                        } else {
                            $field->show()->rules(['required', 'date']);
                        }
                    }
                )
                ->hideFromIndex()
                ->hideFromDetail(function () {
                    /** @var \Novius\LaravelPublishable\Traits\Publishable $this */
                    return ! $this->isPublished();
                }),

            DateTime::make(trans('laravel-nova-publishable::fields.published_at'), $this->resource->getPublishedAtColumn())
                ->nullable()
                ->rules('nullable', 'date')
                ->dependsOn(
                    [$this->resource->getPublicationStatusColumn()],
                    function (DateTime $field, NovaRequest $request, FormData $formData) {
                        if ($formData->{$this->resource->getPublicationStatusColumn()} === PublicationStatus::scheduled->value) {
                            $field->show()->rules(['required', 'date']);
                        } elseif ($formData->{$this->resource->getPublicationStatusColumn()} === PublicationStatus::published->value) {
                            $formData->{$this->resource->getPublishedAtColumn()} = Carbon::now();
                        } else {
                            $field->hide();
                        }
                    }
                )
                ->onlyOnForms(),

            DateTime::make(trans('laravel-nova-publishable::fields.expired_at'), $this->resource->getExpiredAtColumn())
                ->nullable()
                ->rules('nullable', 'date', 'after:'.$this->resource->getPublishedAtColumn())
                ->dependsOn(
                    [$this->resource->getPublicationStatusColumn()],
                    function (DateTime $field, NovaRequest $request, FormData $formData) {
                        if ($formData->{$this->resource->getPublicationStatusColumn()} === PublicationStatus::scheduled->value) {
                            $field->show();
                        } else {
                            $field->hide();
                        }
                    }
                )
                ->onlyOnForms(),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->withNotPublished(PublishableScope::class);
    }
}

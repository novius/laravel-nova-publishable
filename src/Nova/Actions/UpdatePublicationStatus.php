<?php

namespace Novius\LaravelNovaPublishable\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelPublishable\Enums\PublicationStatus;

class UpdatePublicationStatus extends Action
{
    /**
     * Perform the action on the given models.
     */
    public function handle(ActionFields $fields, Collection $models): void
    {
        foreach ($models as $model) {
            $model->publication_status = $fields->get('publication_status');
            if ($fields->get('publication_status') === PublicationStatus::scheduled->value) {
                $model->published_at = $fields->get('published_at');
                $model->expired_at = null;
            }
            $model->save();
        }
    }

    /**
     * Get the fields available on the action.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            Select::make(trans('laravel-nova-publishable::messages.fields.publication_status'), 'publication_status')
                ->options([
                    PublicationStatus::published->value => trans('laravel-nova-publishable::messages.filters.published'),
                    PublicationStatus::unpublished->value => trans('laravel-nova-publishable::messages.filters.not_published'),
                    PublicationStatus::scheduled->value => trans('laravel-nova-publishable::messages.filters.will_be_published'),
                ]),
            DateTime::make(trans('laravel-nova-publishable::messages.fields.published_at'), 'published_at')
                ->dependsOn('publication_status', function (DateTime $field, NovaRequest $request, $formData) {
                    if ($formData->publication_status === PublicationStatus::scheduled->value) {
                        $field->show()->rules(['required', 'date']);
                    } else {
                        $field->hide()->rules(['nullable', 'date']);
                    }
                }),
        ];
    }
}

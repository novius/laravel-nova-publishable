<?php

namespace Novius\LaravelNovaPublishable\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelPublishable\Enums\PublicationStatus;

class UpdatePublicationStatus extends Action
{

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models): void
    {
        foreach ($models as $model) {
            $model->publication_status = $fields->publication_status;
            $model->save();
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Statut', 'publication_status')
                ->options([
                    PublicationStatus::published->value => trans('laravel-nova-publishable::messages.filters.published'),
                    PublicationStatus::unpublished->value => trans('laravel-nova-publishable::messages.filters.not_published'),
                ]),
        ];
    }
}

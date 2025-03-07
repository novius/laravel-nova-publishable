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
     */
    public function handle(ActionFields $fields, Collection $models): void
    {
        foreach ($models as $model) {
            $model->publication_status = $fields->get('publication_status');
            $model->save();
        }
    }

    /**
     * Get the fields available on the action.
     */
    public function fields(NovaRequest $request): array
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

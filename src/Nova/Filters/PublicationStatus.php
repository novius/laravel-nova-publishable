<?php

namespace Novius\LaravelNovaPublishable\Nova\Filters;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelPublishable\Traits\Publishable;

class PublicationStatus extends Filter
{
    /**
     * @param  Builder|Model&Publishable  $query
     */
    public function apply(NovaRequest $request, $query, $value)
    {
        if ($value === 'published') {
            return $query->published();
        }
        if ($value === 'will-be-published') {
            return $query->onlyWillBePublished();
        }
        if ($value === 'not-published') {
            return $query->notPublished();
        }
        if ($value === 'drafted') {
            return $query->onlyDrafted();
        }
        if ($value === 'expired') {
            return $query->onlyExpired();
        }

        return $query;
    }

    public function options(NovaRequest $request): array
    {
        return [
            trans('laravel-nova-publishable::messages.filters.published') => 'published',
            trans('laravel-nova-publishable::messages.filters.will_be_published') => 'will-be-published',
            trans('laravel-nova-publishable::messages.filters.not_published') => 'not-published',
            trans('laravel-nova-publishable::messages.filters.drafted') => 'drafted',
            trans('laravel-nova-publishable::messages.filters.expired') => 'expired',
        ];
    }
}

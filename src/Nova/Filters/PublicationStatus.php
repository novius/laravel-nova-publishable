<?php

namespace Novius\LaravelNovaPublishable\Nova\Filters;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class PublicationStatus extends Filter
{
    public function apply(NovaRequest $request, $query, $value)
    {
        if ($value === 'published') {
            return $query->withoutNotPublished();
        }
        if ($value === 'will-be-published') {
            return $query->onlyWillBePublished();
        }
        if ($value === 'unpublished') {
            return $query->onlyNotPublished();
        }
        if ($value === 'drafted') {
            return $query->onlyDrafted();
        }
        if ($value === 'expired') {
            return $query->onlyExpired();
        }

        return $query;
    }

    public function options(NovaRequest $request)
    {
        return [
            trans('laravel-nova-publishable::filters.published') => 'published',
            trans('laravel-nova-publishable::filters.will_be_published') => 'will-be-published',
            trans('laravel-nova-publishable::filters.unpublished') => 'unpublished',
            trans('laravel-nova-publishable::filters.drafted') => 'drafted',
            trans('laravel-nova-publishable::filters.expired') => 'expired',
        ];
    }
}

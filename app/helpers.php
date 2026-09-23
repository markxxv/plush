<?php

if (! function_exists('localized_route')) {
    function localized_route(
        string $name,
        array $parameters = [],
        ?string $locale = null,
        bool $absolute = true
    ): string {
        $locale ??= app()->getLocale();

        $defaultLocale = config('localization.default');

        $routeName = $locale === $defaultLocale
            ? $name
            : $locale . '.' . $name;

        return route($routeName, $parameters, $absolute);
    }
}

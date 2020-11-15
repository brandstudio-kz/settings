<?php

return [
    'settings_class' => \BrandStudio\Settings\Models\Settings::class,
    'cache_lifetime' => 5,

    'sidebar_icon' => 'la la-cog',

    'use_backpack' => true,
    'crud_middleware' => null,//'role:admin|developer',
    'column_limit' => 40,
];

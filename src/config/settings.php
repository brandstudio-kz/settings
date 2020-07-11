<?php

return [
    'settings_class' => \BrandStudio\Settings\Models\Settings::class,

    'use_backpack' => true,
    'crud_middleware' => 'role:admin|developer',
];

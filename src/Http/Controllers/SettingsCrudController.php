<?php

namespace BrandStudio\Settings\Http\Controllers;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use BrandStudio\Starter\Http\Controllers\DefaultCrudController;

use BrandStudio\Settings\Http\Requests\SettingsRequest;

class SettingsCrudController extends DefaultCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;

    protected $class;
    protected $requestClass = SettingsRequest::class;

    public function __construct()
    {
        parent::__construct();

        $this->class = config('settings.settings_class');


        if (config('settings.crud_middleware')) {
            $this->middleware(config('settings.crud_middleware'));
        }
    }

    public function setup()
    {
        parent::setup();
        CRUD::setRoute(config('backpack.base.route_prefix') . '/settings');
        CRUD::setEntityNameStrings(trans_choice('settings::admin.settings', 1), trans_choice('settings::admin.settings', 2));

        CRUD::addClause('orderBy', 'lft');
        CRUD::addClause('orderBy', 'updated_at', 'desc');

        if (config('app.env') == 'production') {
            CRUD::denyAccess(['create', 'delete']);
        }
    }

    protected function setupListOperation()
    {
        parent::setupListOperation();

        CRUD::addColumns([
            [
                'name' => 'row_number',
                'label' => '#',
                'type' => 'row_number',
            ],
            [
                'name' => 'name',
                'label' => trans('settings::admin.name'),
            ],
            [
                'name' => 'description',
                'label' => trans('settings::admin.description'),
            ],
            [
                'name' => 'value',
                'label' => trans('settings::admin.setting_value'),
                'view_namespace' => 'brandstudio::settings',
                'type' => 'value'
            ],
            [
                'name' => 'updated_at',
                'label' => trans('settings::admin.updated_at'),
                'type' => 'datetime',
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        parent::setupCreateOperation();

        CRUD::addFields([
            [
                'name' => 'key',
                'label' => trans('settings::admin.key'),
                'attributes' => [
                    'required' => true,
                ],
                'wrapperAttributes' => [
                    'class' => 'form-group col-sm-12 required',
                ]
            ],
            [
                'name' => 'name',
                'label' => trans('settings::admin.name'),
                'type' => 'text',
                'attributes' => [
                    'required' => true,
                ],
                'wrapperAttributes' => [
                    'class' => 'form-group col-sm-12 required',
                ],
            ],
            [
                'name' => 'description',
                'label' => trans('settings::admin.description'),
                'type' => 'textarea',
            ],
            [
                'name' => 'field',
                'label' => trans('settings::admin.field'),
                'type' => 'textarea',
                'attributes' => [
                    'required' => true,
                ],
                'wrapperAttributes' => [
                    'class' => 'form-group col-sm-12 required',
                ],
                'default' => json_encode([
                    'name' => 'value',
                    'label' => trans('settings::admin.setting_value'),
                    'type' => 'text',
                ]),
            ],
        ]);
    }

    protected function setupShowOperation()
    {
        CRUD::addColumn([
            'name' => 'key',
            'label' => trans('settings::admin.key'),
        ]);

        parent::setupShowOperation();

        CRUD::removeColumns(['value', 'description']);

        $setting = CRUD::getCurrentEntry();
        $field = json_decode($setting->field, true);
        if (($field['type'] ?? 'text') == 'ckeditor') {
            $field['type'] = 'markdown';
            $field['limit'] = 20000;
        }
        CRUD::addColumn(            [
            'name' => 'description',
            'label' => trans('settings::admin.description'),
            'limit' => 200000,
        ]);
        CRUD::addColumn($field);
    }

    protected function setupUpdateOperation()
    {
        parent::setupUpdateOperation();

        $setting = CRUD::getCurrentEntry();
        $field = json_decode($setting->field, true);
        CRUD::addField($field);
        if (config('app.env') == 'production') {
            CRUD::removeField('field');
            CRUD::removeField('key');
        }
    }
}

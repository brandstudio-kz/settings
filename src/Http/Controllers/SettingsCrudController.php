<?php

namespace BrandStudio\Settings\Http\Controllers;

use BrandStudio\Settings\Http\Requests\SettingsRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SettingsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;


    public function __construct()
    {
        parent::__construct();

        if (config('settings.crud_middleware')) {
            $this->middleware(config('settings.crud_middleware'));
        }
    }

    public function setup()
    {
        $this->crud->setModel(config('settings.settings_class'));
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/settings');
        $this->crud->setEntityNameStrings(trans_choice('settings::admin.settings', 1), trans_choice('settings::admin.settings', 2));
        $this->crud->addClause('orderBy', 'lft');
        $this->crud->addClause('orderBy', 'updated_at', 'desc');
        if (config('app.env') == 'production') {
            $this->crud->denyAccess(['create', 'delete']);
        }
    }

    protected function setupListOperation()
    {
        $this->crud->addColumns([
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
        $this->crud->setValidation(SettingsRequest::class);

        $this->crud->addFields([
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
        $this->crud->addColumn([
            'name' => 'key',
            'label' => trans('settings::admin.key'),
        ]);
        $this->crud->set('show.setFromDb', false);
        $this->setupListOperation();
        $this->crud->removeColumns(['value', 'description']);

        $setting = $this->crud->getCurrentEntry();
        $field = json_decode($setting->field, true);
        if (($field['type'] ?? 'text') == 'ckeditor') {
            $field['type'] = 'markdown';
            $field['limit'] = 20000;
        }
        $this->crud->addColumn(            [
            'name' => 'description',
            'label' => trans('settings::admin.description'),
            'limit' => 200000,
        ]);
        $this->crud->addColumn($field);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        $setting = $this->crud->getCurrentEntry();
        $field = json_decode($setting->field, true);
        $this->crud->addField($field);
        if (config('app.env') == 'production') {
            $this->crud->removeField('field');
            $this->crud->removeField('key');
        }
    }
}

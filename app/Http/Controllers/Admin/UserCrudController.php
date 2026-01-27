<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name'  => 'id',
            'label' => 'ID',
            'type'  => 'number',
        ]);

        CRUD::addColumn([
            'name'  => 'username',
            'label' => 'Логин',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'email',
            'label' => 'Email',
            'type'  => 'email',
        ]);

        CRUD::addColumn([
            'name'  => 'role',
            'label' => 'Роль',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'created_at',
            'label' => 'Создан',
            'type'  => 'datetime',
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserRequest::class);

        CRUD::field('username')->label('Логин');
        CRUD::field('email')->label('Email')->type('email');
        CRUD::field('password')->label('Пароль')->type('password');
        CRUD::field('role')->label('Роль')->type('select_from_array')->options([
            'user' => 'Пользователь',
            'admin' => 'Администратор',
        ])->default('user');
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UserRequest::class);

        CRUD::field('username')->label('Логин');
        CRUD::field('email')->label('Email')->type('email');
        CRUD::field('role')->label('Роль')->type('select_from_array')->options([
            'user' => 'Пользователь',
            'admin' => 'Администратор',
        ])->default('user');
    }
}

<?php

return array(
    'title' => 'Источники',
    'single' => 'Источник',
    'model' => 'App\Models\Source',

    'rules' => [
        'link' => 'required|unique:sources|url'
    ],

    /**
     * The display columns
     */
    'columns' => array(
        'id',
        'name' => ['title' => 'Название'],
        'alias' => ['title' => 'Алиас'],
        'category_name' => array(
            'title' => 'Категория'
        ),
        'created_at' => array(
            'title' => 'Дата создания'
        )
    ),

    'permission' => function() {
        return Auth::user()->is('admin');
    },

    /**
     * The filter set
     */
    'filters' => [
        'id',
        'name' => ['title' => 'Название'],
        'alias' => ['title' => 'Алиас'],
        'created_at' => ['title' => 'Дата создания'],
        'category_id' => ['title' => 'Категория'],
    ],

    /**
     * The editable fields
     */
    'edit_fields' => array(
        'category' => array(
            'title' => 'Категория',
            'type' => 'relationship',
            'name_field' => 'name'
        ),
        'name' => array(
            'title' => 'Название',
            'type' => 'text',
        ),
        'alias' => array(
            'title' => 'Алиас',
            'type' => 'text',
        ),
        'link' => array(
            'title' => 'Ссылка',
            'type' => 'text',
        ),
        'likes' => array(
            'title' => 'Кол-во лайков',
            'type' => 'number'
        )
    ),

);
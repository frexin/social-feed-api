<?php

/**
 * Actors model config
 */

return array(

	'title' => 'Рубрики',

	'single' => 'Рубрика',

	'model' => 'App\Models\Category',

	/**
	 * The display columns
	 */
	'columns' => array(
		'id',
        'name' => ['title' => 'Название'],
        'alias' => ['title' => 'Алиас'],
	),

	/**
	 * The filter set
	 */
	'filters' => array(
		'id',
        'name' => ['title' => 'Название'],
        'alias' => ['title' => 'Алиас'],
	),

	/**
	 * The editable fields
	 */
	'edit_fields' => array(
		'name' => array(
			'title' => 'Название',
			'type' => 'text',
		),
		'alias' => array(
			'title' => 'Алиас',
			'type' => 'text',
		),
        'comments_enabled' => array(
            'type' => 'bool', 'title' => 'Комментарии'
        ),
        'comments_quest' => array(
            'type' => 'bool', 'title' => 'Комментарии для зарег. юзеров'
        ),
        'comments_links' => array(
            'type' => 'bool', 'title' => 'Ссылки в комментариях'
        ),
        'comments_length' => array(
            'type' => 'number', 'title' => 'Макс. длина комментариев'
        ),
	),

);
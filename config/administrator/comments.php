<?php
/**
 * Created by PhpStorm.
 * User: frexin
 * Date: 19.08.2016
 * Time: 22:49
 */

return array(
    'title' => 'Комментарии',
    'single' => 'Комментарий',
    'model' => 'App\Models\Comments',

    /**
     * The display columns
     */
    'columns' => array(
        'id',
        'created_at' => ['title' => 'Дата создания'],
        'post_name' => array(
            'title' => 'Пост'
        ), 'user_name' => array(
            'title' => 'Пользователь'
        ), 'text' => array(
            'title' => 'Текст',
        ),
    ),

    /**
     * The filter set
     */
    'filters' => array(
        'id', 'text' => ['title' => 'Текст'], 'post_id' => ['title' => 'Пост']
    ),

    /**
     * The editable fields
     */
    'edit_fields' => array(
        'text' => array(
            'title' => 'Текст комментария',
            'type' => 'textarea',
        )
    ),

);
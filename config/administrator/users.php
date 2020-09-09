<?php
/**
 * Created by PhpStorm.
 * User: frexin
 * Date: 19.08.2016
 * Time: 22:49
 */

return array(
    'title' => 'Пользователи',
    'single' => 'Пользователь',
    'model' => 'App\Models\User',

    /**
     * The display columns
     */
    'columns' => array(
        'id',
        'name' => [
            'title' => 'Пользователь'
        ],
        'email',
        'role_name' => array(
            'title' => 'Роль'
        ),
        'network' => array(
            'title' => 'Соц. сеть'
        ),
        'created_at' => array(
            'title' => 'Дата Регистрации'
        )
    ),

    'actions' => array(
        'block_comments' => array(
            'title' => 'Блокировать комментарии',
            'confirmation' => 'Вы уверены, что хотите удалить все сообщения этого пользователя?',
            'messages' => array(
                'active' => 'Пожалуйста, подождите',
                'success' => 'Успешно!',
                'error' => 'Возникла ошибка :(',
            ),
            'action' => function(&$model)
            {
                $model->block_comments = 1;
                DB::delete('DELETE FROM comments WHERE user_id = ?', [$model->id]);
                return $model->save();
            }
        ),
    ),

    'permission' => function() {
        return Auth::user()->is('admin');
    },

    /**
     * The filter set
     */
    'filters' => array(
        'id',
        'name' => [
            'title' => 'Пользователь'
        ],
        'email'
    ),

    /**
     * The editable fields
     */
    'edit_fields' => array(
        'roles' => array(
            'title' => 'Роли',
            'type' => 'relationship',
        ),
        'name' => array(
            'title' => 'Имя',
            'type' => 'text',
        ),
        'email' => array(
            'title' => 'E-mail',
            'type' => 'text',
        ),
        'block_comments' => array(
            'title' => 'Заблокировать комментарии',
            'type' => 'bool',
        )
    ),

);
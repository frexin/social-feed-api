<?php

return array(

    /**
     * Settings page title
     *
     * @type string
     */
    'title' => 'Настройки Рекламы',

    /**
     * The edit fields array
     *
     * @type array
     */
    'edit_fields' => array(
        'enable' => array(
            'title' => 'Реклама Включена',
            'type' => 'bool',
        ),
        'posts_count' => array(
            'title' => 'ККол-во постов между рекламой',
            'type' => 'number',
        ),
        'url' => array(
            'title' => 'Ссылка на банер',
            'type' => 'text'
        )
    ),

    /**
     * This is run prior to saving the JSON form data
     *
     * @type function
     * @param array		$data
     *
     * @return string (on error) / void (otherwise)
     */
    'before_save' => function(&$data)
    {
        $advert = \App\Models\Advert::find(1);
        $advert->enable = $data['enable'];
        $advert->posts_count = $data['posts_count'];
        $advert->url = $data['url'];

        $advert->save();

        return true;
    },

    'permission' => function() {
        return Auth::user()->is('admin');
    },

);
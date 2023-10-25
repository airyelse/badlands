<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;

class Formio extends Card
{


    protected $view ="admin::widgets.formio";

    public function __construct($title = '', $content = null)
    {
        Admin::js('https://cdn.form.io/js/formio.full.min.js');
        parent::__construct($title, $content);
    }

}

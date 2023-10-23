<?php

namespace Dcat\Admin\Http\Controllers;

use App\Admin\Repositories\Project;
use App\Models\SysDataField;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class AutoAdminController extends AdminController
{

    public function getFormId()
    {
        return 1;
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Project(), function (Grid $grid) {

            SysDataField::where("form_id", $this->getFormId())->where("grid", true)->each(function (SysDataField $item) use ($grid) {
                $grid->column($item->name, __($item->name));
            });

//            $grid->column('id')->sortable();
//            $grid->column('name');
//            $grid->column('created_at');
//            $grid->column('updated_at')->sortable();

//            $grid->filter(function (Grid\Filter $filter) {
//                $filter->equal('id');
//
//            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Project(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Project(), function (Form $form) {

            SysDataField::where("form_id", $this->getFormId())
                ->each(function (SysDataField $item) use ($form) {
                    $field_type = $item->type;
                    $form->$field_type($item->name);
                });


//            $form->display('id');
//            $form->text('name');

//            $form->display('created_at');
//            $form->display('updated_at');
        });
    }
}

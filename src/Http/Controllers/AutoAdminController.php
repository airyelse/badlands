<?php

namespace Dcat\Admin\Http\Controllers;

use App\Admin\Actions\Show\StartProcessToNext;
use App\Admin\Actions\Show\SubmitOptionToNext;
use App\Http\Controllers\Controller;
use App\Models\Systems\AppModel;
use App\Models\Systems\Data\FieldModel;
use App\Models\Systems\DynamicDataModel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AutoAdminController extends Controller
{


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(mixed $appId)
    {
        $app = AppModel::find($appId);
        if ($app->views()->count() <= 0) {
            abort(403);
        }
        $view = $app->views->first();
        return Grid::make(DynamicDataModel::createModelBy($view->form->connection_name, $view->form->table_name), function (Grid $grid) use ($view) {
//            $grid->scrollbarX();
            $view->fields()->where("grid", true)->orderBy("order")->get()->each(function (FieldModel $item) use ($grid) {
                $grid->column($item->name, __($item->name));
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $appId
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail(mixed $appId, mixed $id)
    {
        $app = AppModel::find($appId);
        if ($app->views()->count() <= 0) {
            abort(403);
        }
        $view = $app->views->first();
        return Show::make($id, DynamicDataModel::createModelBy($view->form->connection_name, $view->form->table_name), function (Show $show)
        use ($app, $view) {
            $view->fields()->where("detail", true)->orderBy("order")->get()->each(function (FieldModel $item) use ($show) {
                $show->field($item->name, __($item->name));
            });
            $show->panel()->tools(function (Show\Tools $tools)
            use ($app, $view) {
                $tools->disableEdit();
                $tools->disableDelete();
                $tools->append((new StartProcessToNext($app->uuid, $view->uuid)));
            });
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form(mixed $appId)
    {
        $view = AppModel::find($appId)->view;
        return Form::make(DynamicDataModel::createModelBy($view->form->connection_name, $view->form->table_name), function (Form $form) use ($view) {
            $view->fields()->where('form', true)->orderBy("order")->get()->each(function (FieldModel $item) use ($form) {
                $field = $form->{$item->typeof()}($item->name, __($item->name));
                foreach ($item->typeOptions() as $key => $value) {
                    $field->$key(...$value);
                }
            });
            $form->disableCreatingCheck();
            $form->disableEditingCheck();
            $form->disableResetButton();
            $form->defaultViewChecked();

        });
    }

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title;

    /**
     * Set description for following 4 action pages.
     *
     * @var array
     */
    protected $description = [
        //        'index'  => 'Index',
        //        'show'   => 'Show',
        //        'edit'   => 'Edit',
        //        'create' => 'Create',
    ];

    /**
     * Set translation path.
     *
     * @var string
     */
    protected $translation;

    /**
     * Get content title.
     *
     * @return string
     */
    protected function title()
    {
        return $this->title ?: admin_trans_label();
    }

    /**
     * Get description for following 4 action pages.
     *
     * @return array
     */
    protected function description()
    {
        return $this->description;
    }

    /**
     * Get translation path.
     *
     * @return string
     */
    protected function translation()
    {
        return $this->translation;
    }

    /**
     * Index interface.
     *
     * @param string $appId
     * @param Content $content
     * @return Content
     */
    public function index(mixed $appId, Content $content)
    {
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['index'] ?? trans('admin.list'))
            ->body($this->grid($appId));
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show(mixed $appId, mixed $id, Content $content)
    {
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['show'] ?? trans('admin.show'))
            ->body($this->detail($appId, $id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit(mixed $appId, mixed $id, Content $content)
    {
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['edit'] ?? trans('admin.edit'))
            ->body($this->form($appId)->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(mixed $appId, Content $content)
    {
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['create'] ?? trans('admin.create'))
            ->body($this->form($appId));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return JsonResponse|RedirectResponse
     */
    public function update(mixed $appId, mixed $id)
    {
        return $this->form($appId)->update($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store(mixed $appId)
    {
        return $this->form($appId)->store();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(mixed $appId, mixed $id)
    {
        return $this->form($appId)->destroy($id);
    }
}

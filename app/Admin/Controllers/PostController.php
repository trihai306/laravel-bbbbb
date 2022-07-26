<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Post;
use App\Models\Province;
use App\Models\RealEstateProject;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

use Dcat\Admin\FormStep\Form as StepForm;
use Dcat\Admin\Widgets\Alert;

class PostController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */

    protected function grid()
    {
        return Grid::make(new Post(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('description');
            $grid->column('user_id');
            $grid->column('body');
            $grid->column('time_end');
            $grid->state('status')->switch();
            $grid->column('status_payment');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

            });
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
        return Show::make($id, new Post(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('user_id');
            $show->field('body');
            $show->field('time_end');
            $show->field('status');
            $show->field('status_payment');
            $show->field('image');
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
        return Form::make(Post::with(['locationinformation', 'basicinformationpost']), function (Form $form) {
            $form->multipleSteps()->remember()->add('Chọn danh mục đăng tin', function (StepForm $step) {
                $step->select('locationinformation.category', 'chọn danh mục')->options([
                    'Bán nhà' => 'Bán nhà',
                    'Bán căn hộ trung cư' => 'Bán căn hộ trung cư',
                    'Bán đất' => 'Bán đất',
                    'Cho thuê nhà' => 'Cho thuê nhà',
                    'Cho thuê căn hộ,chung cư,khách sạn' => 'Ch thuê căn hộ,chung cư,khách sạn',
                    'Cho thuê phòng trọ, nhà trọ' => 'Cho thuê phòng trọ, nhà trọ',
                    'Cho thuê van phòng, mặt bằng' => 'Cho thuê van phòng, mặt bằng',
                    'Cho thuê nhà xưởng, kho, đất' => 'Cho thuê nhà xưởng, kho, đất',
                    'Sang nhượng kiot,Sạp chợ' => 'Sang nhượng kiot,Sạp chợ',
                    'Sang nhượng Quán ăn, Nhà hàng, Khách sạn' => 'Sang nhượng Quán ăn, Nhà hàng, Khách sạn',
                    'Sang nhượng Quán cafe,đồ uống' => 'Sang nhượng Quán cafe,đồ uống',
                    'Sang nhượng Shop thời trang, Tiệm tóc,Spa' => 'Sang nhượng Shop thời trang, Tiệm tóc,Spa',
                    'Sang nhượng shophouse' => 'Sang nhượng shophouse',
                    'Sang nhượng khác' => 'Sang nhượng khác',
                ])->default('Bán nhà')->required();
            })->add('Nhập thông tin địa điểm', function (StepForm $step) {
                $step->select('locationinformation.province', trans('Tỉnh/Thành phố'))
                    ->options(function () {
                        return Province::all()->pluck('name', 'province_id');
                    })
                    ->customFormat(function ($v) {
                        return $v;
                    })->required()
                    ->load(['locationinformation.id_projectbds', 'locationinformation.district'], ['/api/auth/projectbds', '/api/auth/district']);
                $step->select('locationinformation.id_projectbds', trans('Dự án'))
                    ->load(['locationinformation.district','locationinformation.ward'],
                        ['/api/auth/projectdistrict','/api/auth/projectward']);
                $step->select('locationinformation.district', 'Quận/Huyện')->load('locationinformation.ward', '/api/auth/ward')->required();
                $step->select('locationinformation.ward', 'Phường/xã')->required();
                $step->text('locationinformation.address','số nhà')->maxLength(50);
            })->add('Nội dung', function (StepForm $form) {
                $form->text('name');
                $form->text('description');
                $form->hidden('user_id')->value(Admin::user()->getKey());
                $form->markdown('body');
                $form->datetime('time_end');
                $form->select('status')->options([
                    1 => 'hiện',
                    0 => 'ẩn',
                ])->default(1)->required();
                $form->image('image')->autoUpload();
                $form->display('created_at');
                $form->display('updated_at');
            });

        });
    }
}

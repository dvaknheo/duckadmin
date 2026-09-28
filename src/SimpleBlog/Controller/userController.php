<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\SimpleBlog\Controller;

use DuckPhp\Foundation\Controller\UserControllerBase;


class userController extends UserControllerBase
{
    public function index()
    {
        $data =[];
        Helper::Show($data); //TODO 显示我的评论，可以在此删除评论
    }
}

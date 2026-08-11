<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Controller;

use DuckPhp\Foundation\ControllerTrait;

use SimpleBlog\Business\ArticleBusiness;
use SimpleBlog\Business\UserBusiness;

class userController
{
    use ControllerTrait;

    public function __construct()
    {
        $this->initController();
    }
    protected function initController()
    {
        Helper::User()->checkAccess();
    }
    public function index()
    {
        $data =[];
        Helper::User()->Show($data);
    }
}

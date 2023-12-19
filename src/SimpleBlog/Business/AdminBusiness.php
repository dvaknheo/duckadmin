<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Business;

use SimpleBlog\Model\ActionLogModel;
use SimpleBlog\Model\ArticleModel;
use SimpleBlog\Model\UserModel;
use SimpleBlog\Model\CommentModel;

class AdminBusiness extends Base
{

    //////////各种读取列表
    public function getArticle($id)
    {
        $ret = ArticleModel::_()->get($id);
        return $ret;
    }
    public function getCommentList($page = 1, $page_size = 10)
    {
        $ret = CommentModel::_()->getList([], $page, $page_size);
        $ret = [$ret['data'],$ret['count']];
        return $ret;
    }
    public function getLogList($page = 1, $page_size = 10)
    {
        $ret = ActionLogModel::_()->getList([], $page, $page_size);
        $ret = [$ret['data'],$ret['count']];
        return $ret;
    }
    //////////各种操作
    public function addArticle($title, $content)
    {
        $id = ArticleModel::_()->addData($title, $content);
        ActionLogModel::_()->log("添加文章 {$id}", "添加文章");
        return $id;
    }
    public function updateArticle($id, $title, $content)
    {
        $ret = ArticleModel::_()->updateData($id, $title, $content);
        ActionLogModel::_()->log("编辑 ID 为 {$id},原标题，原内容，更改后标题，更改后内容", "编辑文章");
    }
    public function deleteArticle($id)
    {
        $ret = ArticleModel::_()->delete($id);
        ActionLogModel::_()->log("删除 {$id}，结果", "删除文章");
    }
    ///
    public function deleteUser($id)
    {
        $ret = UserModel::_()->delete($id);
        ActionLogModel::_()->log("删除 {$id}，结果", "删除用户");
    }
    public function deleteComment($id)
    {
        $ret = ArticleModel::_()->delete($id);
        ActionLogModel::_()->log("删除 {$id}，结果", "删除评论");
    }
}

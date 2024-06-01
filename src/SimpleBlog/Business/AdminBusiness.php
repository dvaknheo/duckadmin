<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Business;

use SimpleBlog\Model\ActionLogModel;
use SimpleBlog\Model\ArticleModel;
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
        [$total,$data] = CommentModel::_()->getList([], $page, $page_size);
        
        $ids = array_column($data,'user_id');
        $names = Helper::UserService()->batchGetUsernames($ids);
        $titles = ArticleModel::_()->batchGetTitles(array_column($data,'article_id'));
        foreach($data as &$v){
            $v['username']= $names[$v['user_id']]??'--';
            $v['title']= $titles[$v['article_id']]??'--';
        }
        unset($v);
        return [$total,$data];
    }
    //////////各种操作
    public function addArticle($title, $content)
    {
        $id = ArticleModel::_()->addData($title, $content);
        //Helper::AdminService()->log("添加文章 {$id}", "添加文章");
        return $id;
    }
    public function updateArticle($id, $title, $content)
    {
        $ret = ArticleModel::_()->updateData($id, $title, $content);
        //Helper::AdminService()->log("编辑 ID 为 {$id},原标题，原内容，更改后标题，更改后内容", "编辑文章");
    }
    public function deleteArticle($id)
    {
        $ret = ArticleModel::_()->delete($id);
        //Helper::AdminService()->log("删除 {$id}，结果", "删除文章");
    }
    ///
    public function deleteComment($id)
    {
        $ret = CommentModel::_()->delete($id);
        //Helper::AdminService()->log("删除 {$id}，结果", "删除评论");
    }
}

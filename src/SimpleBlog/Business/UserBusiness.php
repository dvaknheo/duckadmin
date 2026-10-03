<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\SimpleBlog\Business;

use DuckAdmin\SimpleBlog\Model\CommentModel;

class UserBusiness extends Base
{
    public function addComment($user_id, $article_id, $content)
    {
        CommentModel::_()->addData($user_id, $article_id, $content);
        // 业务层按框架分层用法是 Helper::UserService()(BusinessHelper 上没有 User():那是控制器层的方法,
        // 原来写 Helper::User() 会落到本命名空间里的 DuckAdmin\SimpleBlog\Business\Helper,
        // 于是 "Call to undefined method ...Helper::User()" 让 /blog/addcomment 每次都 500)
        Helper::UserService()->log($user_id, "{$article_id} 评论成功");
    }
    public function deleteCommentByUser($user_id, $comment_id)
    {
        $comment = CommentModel::_()->get($comment_id);
        Helper::ThrowOn(!$comment, "没找到评论",-1);
        Helper::ThrowOn($comment['user_id'] != $user_id, "不是你的评论", -1);
        CommentModel::_()->delete($comment_id);
        Helper::UserService()->log($user_id, "删除评论成功");
    }
}

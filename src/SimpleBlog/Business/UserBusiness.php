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
        Helper::User()->log("{$article_id} 评论成功");
    }
    public function deleteCommentByUser($user_id, $comment_id)
    {
        $comment = CommentModel::_()->get($comment_id);
        Helper::ThrowOn(!$comment, "没找到评论",-1);
        Helper::ThrowOn($comment['user_id'] != $user_id, "不是你的评论", -1);
        CommentModel::_()->delete($comment_id);
        Helper::User()->log("删除评论成功");
    }
}

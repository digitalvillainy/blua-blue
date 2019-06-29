<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use Neoan3\Apps\Stateless;
use Neoan3\Core\RouteException;
use Neoan3\Frame\Neoan;
use Neoan3\Model\ArticleModel;
use Neoan3\Apps\Db;

class ArticleRating extends Neoan {
    function getArticleRating($obj){

        $rating = ArticleModel::ratingById($obj['articleId']);
        try{
            $jwt = Stateless::restrict();
            $ratedByMe = !empty(Db::easy('article_rating.id',['article_id'=>'$'.$obj['articleId'],'rater_user_id'=>'$'.$jwt['jti']]));
        } catch (RouteException $e){
            $ratedByMe = true;
        }

        return [
            'rating'=>$rating,
            'ratedByMe'=>$ratedByMe
        ];
    }

    function postArticleRating($obj){
        $jwt = Stateless::restrict();
        if(!empty(Db::easy('article_rating.id',['article_id'=>'$'.$obj['articleId'],'rater_user_id'=>'$'.$jwt['jti']]))){
            throw new RouteException('User already rated',400);
        }
        Db::article_rating(['rating'=>$obj['rating'],'rater_user_id'=>'$'.$jwt['jti'],'article_id'=>'$'.$obj['articleId']]);

    }

}

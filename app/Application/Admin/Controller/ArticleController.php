<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Controller\RequestParam\ArticleSubmitRequestParam;
use App\Application\Admin\Model\Article;
use App\Controller\AbstractController;
use Hyperf\DbConnection\Model\Model;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;


#[Controller("/admin/article")]
class ArticleController extends AbstractController
{
    #[Api]
    #[DeleteMapping("delete/{article_id}")]
    function delete(int $article_id)
    {
        $article = Article::find($article_id);
        if (!$article) {
            return $this->returnErrorJson('找不到该记录');
        }

        return $article->delete() ? $this->returnSuccessJson() : $this->returnErrorJson();
    }

    #[Api]
    #[RequestMapping("edit", ["POST", "PUT"])]
    function submitEdit()
    {
        $request_param = new ArticleSubmitRequestParam();
        $request_param->validatedThrowMessage();
        /**
         * @var Article
         */
        $article = Article::firstOrNew([
            'article_id' => $request_param->getArticleId()
        ]);
        $title = $request_param->getTitle();
        $cate = $request_param->getCate();
        $keywords = $request_param->getKeywords();
        $content = $request_param->getContent();
        $res = $article->createArticle($title, $cate, $keywords, $content);

        return $res ? $this->returnSuccessJson(compact('article')) : $this->returnErrorJson();
    }

    #[Api]
    #[GetMapping("edit/{article_id}")]
    function editInfo(int $article_id)
    {
        $cate_list = [
            ['cate'=>'1','cate_name'=>'百度'],
            ['cate'=>'360','cate_name'=>'360'],
        ];
        $article = Article::where('article_id', $article_id)->first() ?: [];

        return compact('cate_list','article');
    }

    #[View]
    #[GetMapping("edit")]
    function edit()
    {
        $article_id = (int)$this->request->input('article_id', 0);

        return ['title' => $article_id > 0 ? '编辑文章' : '新增文章'];
    }

    #[Api]
    #[GetMapping("index/lists")]
    function lists()
    {
        $where = [];
        $title = $this->request->input('title', '');
        $keywords = $this->request->input('keywords', '');
        $cate = $this->request->input('cate', '');

        if (!empty($title)) {
            $where[] = ['title', 'like', "%{$title}%"];
        }
        if (!empty($keywords)) {
            $where[] = ['keywords', 'like', "%{$keywords}%"];
        }
        if (!empty($cate)) {
            $where[] = ['cate', '=', $cate];
        }
        $lists = Article::where($where)
            ->orderBy('created_at', 'DESC')
            ->paginate();

        $cate_list = [
            ['cate'=>'1','cate_name'=>'百度'],
            ['cate'=>'360','cate_name'=>'360'],
        ];
        return compact('lists','cate_list');
    }

    #[View]
    #[GetMapping("index")]
    public function index()
    {
    }
}

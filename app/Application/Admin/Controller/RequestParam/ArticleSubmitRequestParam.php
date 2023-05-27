<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/4 10:29
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Controller\RequestParam;

use App\Annotation\RequestParam;
use App\Controller\RequestParam\BaseRequestParam;

#[RequestParam]
class ArticleSubmitRequestParam extends BaseRequestParam
{
    protected array $rules = [
        'cate' => 'required',
        'title' => 'required',
        'keywords' => 'required',
        'content' => 'required',
    ];

    protected array $message = [
        'cate.required' => '分类必填',
        'title.required' => '标日必填',
        'keywords.required' => '关键词必填',
        'content.required' => '内容必填',
    ];

    private int $article_id = 0;
    private string $title = '';
    private string $cate = '';
    private string $keywords = '';
    private string $content = '';

    /**
     * @return int
     */
    public function getArticleId(): int
    {
        return $this->article_id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getCate(): string
    {
        return $this->cate;
    }

    /**
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @return string
     */
    public function getContent():string
    {
        return $this->content;
    }
}
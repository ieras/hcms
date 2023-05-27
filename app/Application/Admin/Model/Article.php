<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $article_id
 * @property string         $cate
 * @property string         $title
 * @property string         $keywords
 * @property string         $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 * @property-read string    $cate_name
 */
class Article extends Model
{
    use SoftDeletes;

    protected string $primaryKey = 'article_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'articles';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected  array $casts = [
        'article_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['article_id'];

    protected array $appends = ['cate_name'];


    public static $categories = [
        '1'=>'百度',
        '360'=>'360'
    ];

    /**
     * 获取分类名称
     *
     * @return string
     */
    public function getCateNameAttribute(): string
    {
        return self::$categories[$this->cate]??$this->cate;
    }

    /**
     * 创建文章
     *
     * @param string $title
     * @param string $cate
     * @param string $keywords
     * @param string $content
     * @return bool
     */
    public function createArticle(string $title, string $cate, string $keywords, string $content): bool
    {
        $this->title = $title;
        $this->cate = $cate;
        $this->keywords = $keywords;
        $this->content = $content;

        return $this->save();
    }

}
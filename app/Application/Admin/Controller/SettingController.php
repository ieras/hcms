<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/6
 * Time: 22:50.
 */
declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Controller\RequestParam\SettingSubmitRequestParam;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Admin\Model\Setting;
use App\Application\Admin\Service\AdminSettingService;
use App\Controller\AbstractController;
use App\Service\SettingService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="admin/setting")
 */
class SettingController extends AbstractController
{

    /**
     * @Inject()
     */
    protected AdminSettingService $setting;

    /**
     * @Api()
     * @GetMapping(path="site/info")
     */
    function siteInfo()
    {
        $setting = $this->setting->getSiteSetting();

        return compact('setting');
    }

    /**
     * @Api()
     * @PutMapping(path="site")
     */
    function siteSave()
    {
        $setting = $this->request->post('setting', []);
        $res = $this->setting->setSiteSetting($setting);

        return $res ? $this->returnSuccessJson(compact('setting')) : $this->returnErrorJson();
    }

    /**
     * @Api()
     * @GetMapping(path="index/lists")
     */
    function lists()
    {
        $where = [];
        $keyword = $this->request->input('keyword', '');
        $search_setting_group = $this->request->input('setting_group', '');
        if ($keyword) {
            $where[] = ['setting_key', 'like', "%{$keyword}%"];
        }
        if ($search_setting_group !== '') {
            $where[] = ['setting_group', '=', $search_setting_group];
        }
        $lists = Setting::where($where)
            ->orderBy('setting_id', 'DESC')
            ->paginate();
        $setting_group = Setting::where([])
            ->distinct()
            ->pluck('setting_group')
            ->toArray();

        return compact('lists', 'setting_group');
    }

    /**
     * @Api()
     * @PutMapping(path="edit")
     */
    function editSubmit()
    {
        $request_param = new SettingSubmitRequestParam();
        $request_param->validatedThrowMessage();

        $setting_id = $request_param->getSettingId();
        $setting = Setting::updateOrCreate(['setting_id' => $setting_id], [
            'setting_key' => $request_param->getSettingKey(),
            'setting_value' => $request_param->getSettingValue(),
            'setting_description' => $request_param->getSettingDescription(),
            'setting_group' => $request_param->getSettingGroup(),
            'type' => $request_param->getType(),
        ]);

        //清空指定分组的缓存
        SettingService::getInstance()
            ->flushCache($setting->setting_group);

        return $setting ? $this->returnSuccessJson(compact('setting')) : $this->returnErrorJson();
    }

    /**
     * @Api()
     * @GetMapping(path="edit/{setting_id}")
     */
    function editInfo(int $setting_id = 0)
    {
        $setting = Setting::find($setting_id) ?: [];

        return compact('setting');
    }

    /**
     * @View()
     * @GetMapping(path="site")
     */
    function site() { }

    /**
     * @Api()
     * @DeleteMapping(path="delete/{setting_id}")
     */
    function settingDelete(int $setting_id = 0)
    {
        $setting = Setting::find($setting_id) ?: [];
        if (!$setting) {
            return $this->returnErrorJson('抱歉，找不到该配置');
        }
        $setting_group = $setting->setting_group;
        if ($setting->delete()) {
            SettingService::getInstance()
                ->flushCache($setting_group);

            return [];
        } else {
            return $this->returnErrorJson();
        }
    }

    /**
     * @View()
     * @GetMapping(path="edit")
     */
    function edit()
    {
        $setting_id = (int)$this->request->input('setting_id', 0);

        return ['title' => $setting_id > 0 ? '编辑配置' : '新增配置'];
    }


    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index() { }
}
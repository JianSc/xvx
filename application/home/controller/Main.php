<?php

namespace app\home\controller;

use think\Config;
use think\Controller;
use think\Db;

/**
 * Created by PhpStorm.
 * User: Ganzi
 * Date: 2020-02-28
 * Time: 16:30
 */
class Main extends Controller
{
//    public function _empty()
//    {
//        return 'error';
//    }

    //首页
    public function index()
    {
        $matches = null;
        error_reporting(E_ALL ^ E_NOTICE);
        preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
        $lang = $matches[1];
        //echo $lang;
        if ($lang!='zh-CN')
        {
            return $this->redirect('https://en.xvxchannel.com');
        }
        $this->assign('title', Config::get('WEB_TITLE'));
        $this->assign('menu', 1);

        $db = Db::table('adpost')
            ->order('id', 'desc')
            ->limit(6)
            ->select();

        $this->assign('db', $db);

        return $this->fetch();
    }

    //新品
    public function newlaunch()
    {
        $this->assign('title', Config::get('WEB_TITLE'));
        $this->assign('menu', 2);

        $db = Db::table('xnewgoods')
            ->order('id', 'desc')
            ->limit(4)->select();

        $this->assign('db', $db);

        return $this->fetch();
    }

    public function newlaunchtxt()
    {
        $this->assign('title', Config::get('WEB_TITLE'));
        $this->assign('menu', 2);

        $id = input('?get.id') ? input('get.id') : 0;
        $id = intval($id);
        if ($id == 0) {
            return $this->redirect('/');
        }
        $db = Db::table('xnewgoods')
            ->where('id', $id)
            ->find();
        if ($db == null) {
            $this->redirect('/');
        }

        $this->assign('db', $db);
        return $this->fetch();
    }

    //产品
    public function products()
    {
        $this->assign('title', Config::get('WEB_TITLE'));
        $this->assign('menu', 3);

        $id = input('?get.id') ? input('get.id') : 0;
        $id = intval($id);
        if ($id == 0) {
            $this->redirect('/');
        }

        $db = Db::table('xproducts')
            ->where('type', $id)
            ->order('id', 'desc')
            ->paginate(8, false, [
                'query' => ['id' => $id]
            ]);
        $imgarr = array();

        foreach ($db as $r) {
            $info = Db::table('xproducts_img')
                ->where('panid', $r['id'])
                ->find();
            array_push($imgarr, $info['name']);
        }

        $this->assign('id', $id);
        $this->assign('db', $db);
        $this->assign('imgarr', $imgarr);

        return $this->fetch();
    }

    public function product()
    {
        $this->assign('title', Config::get('WEB_TITLE'));
        $this->assign('menu', 3);

        $id = input('?get.id') ? input('get.id') : 0;
        $id = intval($id);
        $id = ($id == 0) ? 1 : $id;

        $db = Db::table('xproducts')
            ->where('id', $id)
            ->find();
        $imgarr = Db::table('xproducts_img')
            ->where('panid', $id)
            ->select();

        $this->assign('db', $db);
        $this->assign('imgarr', $imgarr);

        return $this->fetch();
    }

    //下载
    public function download()
    {
        $this->assign('title', Config::get('WEB_TITLE'));
        $this->assign('menu', 4);

        $id = input('?get.id') ? input('get.id') : 0;
        $id = intval($id);
        $id = ($id == 0) ? 1 : $id;

        $db = Db::table('xproducts')
            ->where('type', $id)
            ->where('d', 1)
            ->order('id', 'desc')
            ->paginate(8, false, [
                'query' => ['id' => $id]
            ]);
        $imgarr = array();

        foreach ($db as $r) {
            $info = Db::table('xproducts_img')
                ->where('panid', $r['id'])
                ->find();
            array_push($imgarr, $info['name']);
        }

        $this->assign('id', $id);
        $this->assign('db', $db);
        $this->assign('imgarr', $imgarr);

        return $this->fetch();
    }

    public function down()
    {
        $this->assign('title', Config::get('WEB_TITLE'));
        $id = input('?get.id') ? input('get.id') : 0;
        $id = intval($id);
        if ($id == 0) {
            die('');
        }

        $db = Db::table('xdownload')
            ->where('pid', $id)
            ->select();

        $this->assign('db', $db);
        return $this->fetch();

    }

    //支持
    public function support()
    {
        $this->assign('title', Config::get('WEB_TITLE'));
        $this->assign('menu', 5);

        return $this->fetch();
    }

    public function aboutus()
    {
        $this->assign('title', Config::get('WEB_TITLE'));
        $this->assign('menu', 6);

        return $this->fetch();
    }
}
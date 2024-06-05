<?php

namespace app\home\controller;

use think\Controller;
use think\Db;


class Manage extends controller
{
    public function index()
    {
        $this->assign('title', '后台管理');

        //return $this->fetch('manage/logo');
        if (!session('?name')) {
            return $this->fetch('logo');
        } else {
            $this->assign('menu', 1);
            return $this->fetch('newgoods');
        }

    }

    public function download()
    {
        $this->assign('title', '后台管理');
        if (!session('?name')) {
            die('安全连接已超时，请重新<a href="/manage">登陆</a>！');
        }
        $this->assign('menu', 3);
        return $this->fetch();
    }

    public function download_edit()
    {
        $this->assign('title', '后台管理');
        if (!session('?name')) {
            die('安全连接已超时，请重新<a href="/manage">登陆</a>！');
        }
        $this->assign('menu', 3);

        $db = Db::table('xdownload')
            ->order('id', 'desc')
            ->paginate(15);

        $this->assign('db_xdownload', $db);

        return $this->fetch();
    }

    public function newgoods()
    {
        $this->assign('title', '后台管理');
        if (!session('?name')) {
            die('安全连接已超时，请重新<a href="/manage">登陆</a>！');
        }
        $this->assign('menu', 1);
        return $this->fetch();
    }

    public function newgoods_e()
    {
        $this->assign('title', '后台管理');
        if (!session('?name')) {
            die('安全连接已超时，请重新<a href="/manage">登陆</a>！');
        }
        $id = input('?get.id') ? input('get.id') : 0;
        $id = intval($id);

        if ($id == 0) {
            return $this->fetch('newgoods');
        }

        $this->assign('menu', 1);

        $db = Db::table('xnewgoods')
            ->where('id', $id)
            ->find();

        if ($db == null) {
            return $this->fetch('newgoods');
        }

        $this->assign('db', $db);
        $this->assign('id', $id);
        return $this->fetch();
    }

    public function newgoodsedit()
    {
        $this->assign('title', '后台管理');
        if (!session('?name')) {
            die('安全连接已超时，请重新<a href="/manage">登陆</a>！');
        }
        $db_xnewgoods = Db::table('xnewgoods')
            ->order('id', 'desc')
            ->paginate(15);
        $this->assign('db_xnewgoods', $db_xnewgoods);
        $this->assign('menu', 1);
        return $this->fetch();
    }

    public function goods()
    {
        $this->assign('title', '后台管理');
        if (!session('?name')) {
            die('安全连接已超时，请重新<a href="/manage">登陆</a>！');
        }
        $this->assign('menu', 2);
        return $this->fetch('products');
    }

    public function products_edit()
    {
        $this->assign('title', '后台管理');
        if (!session('?name')) {
            die('安全连接已超时，请重新<a href="/manage">登陆</a>！');
        }
        $this->assign('menu', 2);
        $db = Db::table('xproducts')
            ->order('id', 'desc')
            ->paginate(15);
        $this->assign('db_xproducts', $db);
        return $this->fetch();
    }

    public function adpost()
    {
        $this->assign('title', '后台管理');
        if (!session('?name')) {
            die('安全连接已超时，请重新<a href="/manage">登陆</a>！');
        }
        $this->assign('menu', 4);

        return $this->fetch();
    }

    public function adpost_e()
    {
        $this->assign('title', '后台管理');
        if (!session('?name')) {
            die('安全连接已超时，请重新<a href="/manage">登陆</a>！');
        }
        $this->assign('menu', 4);

        $dbs = Db::table('adpost')
            ->order('id', 'desc')
            ->paginate(15);

        $this->assign('dbs', $dbs);

        return $this->fetch();
    }

    public function support()
    {
        $this->assign('title', '后台管理');
        if (!session('?name')) {
            die('安全连接已超时，请重新<a href="/manage">登陆</a>！');
        }
        $this->assign('menu', 5);

        $info = Db::table('xsupprot')
            ->order('id', 'desc')
            ->paginate(15);

        $this->assign('dbs', $info);

        return $this->fetch();
    }

    public function xsys()
    {
        $this->assign('title', '后台管理');
        if (!session('?name')) {
            die('安全连接已超时，请重新<a href="/manage">登陆</a>！');
        }
        $this->assign('menu', 6);

        return $this->fetch();
    }

    public function products_e()
    {
        if (!session('?name')) {
            die('安全连接已超时，请重新<a href="/manage">登陆</a>！');
        }

        $id = input('?get.id')?input('get.id'):'';
        if ($id=='')
        {
            return $this->redirect('/home/manage/products_edit.html');
        }
        $db = Db::table('xproducts')
            ->where('id',$id)
            ->find();
        if ($db==null)
        {
            return $this->redirect('/home/manage/products_edit.html');
        }
        $this->assign([
            'db'=>$db,
            'id'=>$id,
            'title'=>'商品信息修改'
        ]);
        return $this->fetch();
    }

    public function quit()
    {
        session('name', null);
        session('pwd', null);
        $this->assign('title', '后台管理');
        return $this->fetch('logo');
    }
}
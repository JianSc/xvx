<?php
/**
 * Created by PhpStorm.
 * User: Ganzi
 * Date: 2021/5/13
 * Time: 13:40
 */

namespace app\home\controller;

use think\Controller;
use think\Db;

class Handles extends Controller
{
    public function add_newgoods()
    {
        if (!session('?name')) {
            return json(['t' => 'err', 'm' => '安全连接已断开。']);
        }

        $title = input('?post.title') ? input('post.title') : '';
        $val = input('?post.contentval') ? input('post.contentval') : '';
        $url = input('?post.url') ? input('post.url') : '#';

        $arrval = array("\r\n", "\r", "\n");
        $val = str_replace($arrval, '<br>', $val);
        $imgurl = '';

        if ($title == '' || $val == '') {
            return json(['t' => 'err', 'm' => '标题或内容不能为空！']);
        }
        $url = ($url == '') ? '#' : $url;

        $file = request()->file('file');
        if ($file == null) {
            return json(['t' => 'err', 'm' => '请上传图片！']);
        }

        $info = $file->rule('uniqid')->move(ROOT_PATH . 'www' . DS . 'upload' . DS . 'launch');
        if (!$info) {
            return json(['t' => 'err', 'm' => $file->getError()]);
        }
        $imgurl = $info->getSaveName();

        Db::table('xnewgoods')->insert([
            'time' => date('Y-m-d H:i:s'),
            'title' => $title,
            'content' => $val,
            'imgurl' => $imgurl,
            'url' => $url
        ]);


        return json(['t' => 'suc', 'm' => '操作成功！']);
    }

    public function add_products()
    {
        if (!session('?name')) {
            return json(['t' => 'err', 'm' => '安全连接已断开。']);
        }

        $title = input('?post.title') ? input('post.title') : '';
        if ($title == '') {
            return json(['t' => 'err', 'm' => '商品标题不能为空！']);
        }
        $url = input('?post.url') ? input('post.url') : '';
        $money = input('?post.money') ? input('post.money') : 0;
        $money = doubleval($money);
        $val = input('?post.contentval') ? input('post.contentval') : '';
        $files = request()->file('upimg');
        $type = input('?post.type') ? input('post.type') : '';
        $val2 = input('?post.val2') ? input('post.val2') : '';
        $order = array("\r\n", "\r", "\n");
        $val2 = str_replace($order, '<br>', $val2);
        if ($type == '') {
            return json(['t' => 'err', 'm' => '请选择商品类别！']);
        }

        if (count($files) < 1 || count($files) > 6) {
            return json(['t' => 'err', 'm' => '请将上传的商品图片保持在1~6张。']);
        }

        $imgname = array();
        foreach ($files as $f) {
            $info = $f->rule('uniqid')->move(ROOT_PATH . 'www' . DS . 'upload' . DS . 'goods');
            if ($info) {
                array_push($imgname, $info->getSavename());
            }
        }

        $dbid = Db::table('xproducts')
            ->insertGetId([
                'time' => date('Y-m-d H:i:s'),
                'title' => $title,
                'money' => $money,
                'url' => $url,
                'val' => $val,
                'type' => $type,
                'val2' => $val2
            ]);

        foreach ($imgname as $item) {
            Db::table('xproducts_img')
                ->insert([
                    'time' => date('Y-m-d H:i:s'),
                    'name' => $item,
                    'panid' => $dbid
                ]);
        }

        $this->stat_upzone($val);
        $this->del_upzone();

        return json(['t' => 'suc', 'm' => '新商品添加成功！']);
    }

    public function add_download()
    {
        if (!session('?name')) {
            return json(['t' => 'err', 'm' => '安全连接已断开。']);
        }
        $title = input('?post.title') ? input('post.title') : '';
        $val = input('?post.contentval') ? input('post.contentval') : '';
        $files = request()->file('file');
        $order = array("\r\n", "\r", "\n");
        $val = str_replace($order, '<br>', $val);
        $panid = input('?post.t2') ? input('post.t2') : 0;

        if ($title == '' || $val == '') {
            return json(['t' => 'err', 'm' => '标题与简介内容不能为空！']);
        }
        if ($files == null) {
            return json(['t' => 'err', 'm' => '上传附件不能为空！']);
        }
        $info = $files->rule('uniqid')->move(ROOT_PATH . 'www' . DS . 'upload' . DS . 'download');
        if ($info == null) {
            return json(['t' => 'err', 'm' => $files->getError()]);
        }
        $rarname = $info->getSaveName();
        $rarsize = $info->getSize();

        Db::table('xdownload')
            ->insert([
                'time' => date('Y-m-d H:i:s'),
                'title' => $title,
                'val' => $val,
                'downname' => $rarname,
                'size' => $rarsize,
                'pid' => $panid
            ]);

        Db::table('xproducts')
            ->where('id', $panid)
            ->update([
                'd' => 1
            ]);

        return json(['t' => 'suc', 'm' => '操作成功！']);
    }

    public function del_download()
    {
        if (!session('?name')) {
            return json(['t' => 'err', 'm' => '安全连接已断开。']);
        }

        $id = input('?post.id') ? input('post.id') : 0;
        $id = intval($id);
        if ($id == 0) {
            return json(['t' => 'err', 'm' => '非法操作！']);
        }

        $info = Db::table('xdownload')
            ->where('id', $id)
            ->find();

        if ($info == null) {
            return json(['t' => 'err', 'm' => '数据不存在！']);
        }

        $panid = $info['pid'];

        $filename = $info['downname'];
        $filename = ROOT_PATH . '/www/upload/download/' . $filename;
        if (file_exists($filename)) {
            unlink($filename);
        }

        Db::table('xdownload')
            ->where('id', $id)
            ->delete();

        $infocount = Db::table('xdownload')
            ->where('pid', $panid)
            ->select();

        if (count($infocount) < 1) {
            Db::table('xproducts')
                ->where('id', $panid)
                ->update([
                    'd' => 0
                ]);
        }

        return json(['t' => 'suc', 'm' => '操作成功！']);
    }

    private function stat_upzone($urlstr)
    {
        $imgurl = '';
        preg_match_all("/(href|src)=([\"|']?)([^\"'>]+.(jpg|JPG|jpeg|JPEG|gif|GIF|png|PNG))/i", $urlstr, $imgurl);
        if ($imgurl != '') ;
        {
            foreach ($imgurl[3] as $r) {
                $parts = explode('/', $r);
                Db::table('xupload_log')
                    ->where('name', $parts[4])
                    ->update([
                        'v' => true
                    ]);
            }
        }
    }

    private function del_upzone()
    {
        $db = Db::table('xupload_log')
            ->where('v', false)
            ->select();
        foreach ($db as $r) {
            $a = ROOT_PATH . '/www/upload/zone/' . $r['name'];
            if (file_exists($a)) {
                unlink($a);
            }

        }

        Db::table('xupload_log')
            ->where('v', false)
            ->delete();

    }

    public function del_newgoods()
    {
        if (!session('?name')) {
            return json(['t' => 'err', 'm' => '安丛连接已断开。']);
        }

        $id = input('?post.id') ? input('post.id') : 0;
        $id = intval($id);

        if ($id == 0) {
            return json(['t' => 'err', 'm' => '非法操作！']);
        }

        $info = Db::table('xnewgoods')
            ->where('id', $id)
            ->find();
        if ($info != null) {
            $img = $info['imgurl'];
            $r = ROOT_PATH . '/www/upload/launch/' . $img;
            if (file_exists($r)) {
                unlink($r);
            }
        }

        Db::table('xnewgoods')
            ->where('id', $id)
            ->delete();

        return json(['t' => 'suc', 'm' => '操作成功！']);

    }

    public function del_products()
    {
        if (!session('?name')) {
            return json(['t' => 'err', 'm' => '安丛连接已断开。']);
        }

        $id = input('?post.id') ? input('post.id') : 0;
        $id = intval($id);
        if ($id == 0) {
            return json(['t' => 'err', 'm' => '非法操作！']);
        }
        $img = Db::table('xproducts_img')
            ->where('panid', $id)
            ->select();
        foreach ($img as $r) {
            $a = ROOT_PATH . '/www/upload/goods/' . $r['name'];
            if (file_exists($a)) {
                unlink($a);
            }
        }

        $info = Db::table('xproducts')
            ->where('id', $id)
            ->find();
        if ($info != null) {
            $src = $info['val'];
            preg_match_all("/(href|src)=([\"|']?)([^\"'>]+.(jpg|JPG|jpeg|JPEG|gif|GIF|png|PNG))/i", $src, $src);
            foreach ($src[3] as $r) {
                $a = ROOT_PATH . $r;
                if (file_exists($a)) {
                    unlink($a);
                }
            }
        }

        Db::table('xproducts_img')
            ->where('panid', $id)
            ->delete();

        Db::table('xproducts')
            ->where('id', $id)
            ->delete();

        return json(['t' => 'suc', 'm' => '操作成功！']);
    }

    //TODO:xEditor富文本编辑器文件上传模块
    public function xeditupload()
    {

        //xEditor富文本编辑器，必须要插入编辑器的页面引入xEditor的css文件
        //xEditor回传JSON数据，必须用 json_encode
        //xEditor回传JSON数据，返回图片路径必须为全路径+文件名

        if (!session('?name')) {
            return json_encode(['t' => 'err', 'm' => '安全连接已断开。']);
        }

        $file = request()->file('file');
        if (!$file) {
            return json_encode(['t' => 'err', 'm' => '上传文件为空！']);
        }

        $info = $file->rule('uniqid')->validate(['ext' => 'jpg,jpeg,png,gif'])->move(ROOT_PATH . 'www' . DS . 'upload' . DS . 'zone');

        if ($info) {
            //TODO:图片上传成功数据处理
            Db::table('xupload_log')
                ->insert([
                    'time' => date('Y-m-d H:i:s'),
                    'name' => $info->getSaveName(),
                    'size' => $info->getSize(),
                    'ext' => $info->getExtension()
                ]);
            return json_encode(['t' => 'suc', 'm' => '/www/upload/zone/' . $info->getSaveName()]);
        } else {
            return json_encode(['t' => 'err', 'm' => $file->getError()]);
        }

    }

    public function userage()
    {
        $type = input('?get.type') ? input('get.type') : '';

        //登陆处理
        if ($type == 'lend') {
            $name = input('?post.name') ? input('post.name') : '';
            $pwd = input('?post.pwd') ? input('post.pwd') : '';
            $code = input('?post.code') ? input('post.code') : '';
            if ($name == '' || $pwd == '' || $code == '') {
                return json(['t' => 'err', 'm' => '验证失败！']);
            }
            if (!captcha_check($code)) {
                return json(['t' => 'err', 'm' => '验证码错误！']);
            }
            $rst = Db::table('xusers')
                ->where([
                    'name' => $name,
                    'pwd' => md5($pwd)
                ])
                ->find();

            if ($rst == null) {
                return json(['t' => 'err', 'm' => '用户名/密码错误！']);
            } else {
                session('name', $name);
                session('pwd', $pwd);
                return json(['t' => 'suc']);
            }
        }
    }

    public function gettype()
    {
        $id = input('?post.id') ? input('post.id') : 0;
        $id = intval($id);
        if ($id == 0) {
            return json(['t' => 'err']);
        }

        $info = Db::table('xproducts')
            ->where('type', $id)
            ->select();

        if (count($info) < 1) {
            return json(['t' => 'err']);
        }

        $arrtitle = '';
        foreach ($info as $item) {
            $arrtitle .= $item['title'] . '<kpt>' . $item['id'] . '<bbt>';
        }

        $arrtitle = strlen($arrtitle) > 0 ? substr($arrtitle, 0, strlen($arrtitle) - 5) : $arrtitle;

        return json(['t' => 'suc', 'm' => $arrtitle]);
    }

    public function del_support()
    {
        if (!session('?name')) {
            return json(['t' => 'err', 'm' => '安全连接已断开。']);
        }

        $id = input('?post.id') ? input('post.id') : 0;
        $id = intval($id);

        Db::table('xsupprot')
            ->where('id', $id)
            ->delete();

        return json(['t' => 'suc', 'm' => '操作成功！']);
    }

    public function add_adpost()
    {
        if (!session('?name')) {
            return json(['t' => 'err', 'm' => '安全连接已断开。']);
        }

        $pid = input('?post.t2') ? input('post.t2') : '0';
        $pid = intval($pid);
        if ($pid == 0) {
            return json(['t' => 'err', 'm' => '请选择绑定的产品！']);
        }

        $file = request()->file('file');
        if (!$file) {
            return json(['t' => 'err', 'm' => '请上传图片！']);
        }
        $info = $file->rule('uniqid')->move(ROOT_PATH . 'www' . DS . 'upload' . DS . 'newlist');
        if (!$info) {
            return json(['t' => 'err', 'm' => $file->getError()]);
        }
        $src = $info->getSaveName();

        Db::table('adpost')
            ->insert([
                'src' => $src,
                'pid' => $pid,
                'time' => date('Y-m-d H:i:s')
            ]);
        return json(['t' => 'suc', 'm' => '操作成功！']);

    }

    public function support()
    {
        $name = input('?post.name') ? input('post.name') : '';
        $phone = input('?post.phone') ? input('post.phone') : '';
        $country = input('?post.country') ? input('post.country') : '';
        $email = input('?post.email') ? input('post.email') : '';
        $pro = input('?post.pro') ? input('post.pro') : '';
        $sn = input('?post.sn') ? input('post.sn') : '';
        $mes = input('?post.mes') ? input('post.mes') : '';
        $strarr = array("\r\n", "\r", "\n");
        $mes = str_replace($strarr, '<br>', $mes);

        if ($name == '' || $sn == '' || $country == '' || $mes == '') {
            return json(['t' => 'err', 'm' => '请完整填写您的反馈信息']);
        }

        $info = Db::table('xsupprot')
            ->where('sn', $sn)
            ->find();
        if ($info != null) {
            return json(['t' => 'err', 'm' => '您的问题正在处理中。']);
        }

        Db::table('xsupprot')
            ->insert([
                'time' => date('Y-m-d H:i:s'),
                'name' => $name,
                'phone' => $phone,
                'country' => $country,
                'email' => $email,
                'product' => $pro,
                'sn' => $sn,
                'message' => $mes
            ]);

        return json(['t' => 'suc', 'm' => '您的问题已提交，我们将尽快处理，感谢您的支持！！']);
    }

    public function del_adpost()
    {
        if (!session('?name')) {
            return json(['t' => 'err', 'm' => '安全连接已断开。']);
        }

        $id = input('?post.id') ? input('post.id') : 0;
        $id = intval($id);
        if ($id == 0) {
            return json(['t' => 'err', 'm' => '非法操作！']);
        }

        $src_db = Db::table('adpost')
            ->where('id', $id)
            ->find();

        if ($src_db != null) {
            $src = $src_db['src'];
            $src = ROOT_PATH . '/www/upload/newlist/' . $src;
            if (file_exists($src)) {
                unlink($src);
            }
        }

        Db::table('adpost')
            ->where('id', $id)
            ->delete();

        return json(['t' => 'suc', 'm' => '操作成功！']);
    }

    public function edit_pwd()
    {
        if (!session('?name')) {
            return json(['t' => 'err', 'm' => '安全连接已断开。']);
        }

        $pwd1 = input('?post.pwd1') ? input('post.pwd1') : '';
        $pwd2 = input('?post.pwd2') ? input('post.pwd2') : '';
        $pwd3 = input('?post.pwd3') ? input('post.pwd3') : '';

        if ($pwd1 == '') {
            return json(['t' => 'err', 'm' => '请输入原密码！']);
        }
        if ($pwd2 == '' || $pwd3 == '') {
            return json(['t' => 'err', 'm' => '请输入新密码！']);
        }
        if ($pwd2 != $pwd3) {
            return json(['t' => 'err', 'm' => '两次输入的新密码不一致！']);
        }

        $name = session('name');
        $info = Db::table('xusers')
            ->where([
                'name' => $name,
                'pwd' => md5($pwd1)
            ])
            ->find();

        if ($info == null) {
            return json(['t' => 'err', 'm' => '你的密码输入有误，拒绝修改！']);
        }

        Db::table('xusers')
            ->where('name', $name)
            ->update([
                'pwd' => md5($pwd2)
            ]);

        return json(['t' => 'suc', 'm' => '密码修改成功！']);
    }
    public function edit_newgoods()
    {
        if (!session('?name')) {
            return json(['t' => 'err', 'm' => '安全连接已断开。']);
        }
        $val = input('?post.val')?input('post.val'):'';
        $title = input('?post.title')?input('post.title'):'';
        $id=input('?post.id')?input('post.id'):'';
        if ($val==''||$title==''||$id=='')
        {
            return json(['t'=>'err','m'=>'标题或内容为空！']);
        }
        Db::table('xnewgoods')
            ->where('id',$id)
            ->update([
                'content'=>$val,
                'title'=>$title
            ]);
        return json(['t'=>'suc','m'=>'操作成功！']);
    }

    public function edit_products()
    {
        if (!session('?name')) {
            return json(['t' => 'err', 'm' => '安全连接已断开。']);
        }
        $id = input('?post.id')?input('post.id'):'';
        $val = input('?post.contentval')?input('post.contentval'):'';
        $title = input('?post.title')?input('post.title'):'';
        if ($id==''||$val==''||$title=='')
        {
            return json(['t'=>'err','m'=>'标题或内容为空！']);
        }
        Db::table('xproducts')
            ->where('id',$id)
            ->update([
                'title'=>$title,
                'val'=>$val
            ]);
        return json(['t'=>'suc','m'=>'修改成功！']);
    }

}
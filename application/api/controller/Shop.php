<?php


namespace app\api\controller;


use app\common\controller\Api;
use think\Request;
use app\admin\model\Shop as ShopModel;
use app\admin\model\Shopdingdan as ShopdingdanModel;
use app\admin\model\User as Usermodel;
use app\admin\model\card\Card as Cardmodel;
use app\admin\model\Moneylog as Moneylogmodel;
use app\admin\model\Jifenlog as Jifenlogmodel;
use think\Log;

class Shop extends Api
{
	protected $noNeedLogin = ['*'];


	public function getlist()
	{
		$productlist = ShopModel::select();
		$url = 'http://' . $_SERVER['SERVER_NAME'];
		$newlist = [];
		foreach ($productlist as $k => $v) {
			$newlist[$k]['id'] = $v['id'];
			$newlist[$k]['type'] = $v['type'];
			$newlist[$k]['card_id'] = $v['card_id'];
			$newlist[$k]['title'] = $v['title'];
			$newlist[$k]['miaoshu'] = $v['miaoshu'];
			$newlist[$k]['mone'] = $v['mone'];
			$newlist[$k]['yue'] = $v['yue'];
			$newlist[$k]['jifen'] = $v['jifen'];
			$picurl = $v['picurl'];
			$find = 'http';

			if (strstr($picurl, $find)) {
				$newlist[$k]['picurl'] = $v['picurl'];
			} else {
				$newlist[$k]['picurl'] = $url . $v['picurl'];
			}
		}

		$this->result('商品列表', $newlist, 200);
	}

	public function getdetail($id)
	{
		//$req = $request->post();
		$productModel = ShopModel::where(['id' => $id])->find();
		$url = 'http://' . $_SERVER['SERVER_NAME'];
		$picurl = $productModel['picurl'];
		$find = 'http';

		if (strstr($picurl, $find)) {
			$productModel['picurl'] = $picurl;
		} else {
			$productModel['picurl'] = $url . $picurl;
		}

		if ($productModel['type'] == 0) {
			$url = 'http://' . $_SERVER['SERVER_NAME'];
			$miaoshu = $productModel['miaoshu'];

			if (strpos($miaoshu, 'http://') == false && strpos($miaoshu, 'https://') == false) {
				$productModel['miaoshu'] = str_replace('src="', 'src="http://' . $_SERVER['SERVER_NAME'], $miaoshu);
			}



			Log::info($productModel);
		}

		$this->result('商品详情', $productModel, 200);
	}

	// 积分兑换
	public function shopxd(Request $request)
	{
		$req = $request->post();

		$userid = $req['userid'];
		$usernamereq	= $req['username'];
		$paytype = $req['paytype'];
		$pid = $req['pid'];

		$username = Usermodel::where('id', $userid)->find();

		$shop = ShopModel::where('id', $pid)->find();

		if ($paytype == '积分') {
			//判断用户积分是否足够扣款
			if ($shop['jifen'] > $username['integral']) {
				return json(['code' => 2, 'msg' => '兑换失败，积分不足!']);
				die;
			}
		} else {
			//判断用户余额是否足够扣款strval
			if ($shop['yue'] > $username['money']) {
				return json(['code' => 2, 'msg' => '购买失败，余额不足!']);
				die;
			}
		}
		// 判断收货地址是否为空 20201211 libra
		if ($shop['type'] == 0) {
			if ($req['shdz'] == '') {
				return json(['code' => 2, 'msg' => '购买失败，收获地址不得为空!']);
				die;
			}
		}

		$shopdingdanModel = new ShopdingdanModel();
		if ($paytype == '积分') {

			//$integral['integral'] = $username['integral'] - $shop['jifen'];
			$shengyu_jifen = $username['integral'] - $shop['jifen'];
			if (Usermodel::where('id', $userid)->update(['integral' => $shengyu_jifen])) {
				$insert['uid']	=	$userid;
				$insert['username']	=	$usernamereq;
				$orderid =   date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
				$insert['payjiage']  = $shop['jifen'];
				$insert['spid']	=	$pid;
				//$insert['spname']	=	$shop['title'];
				$insert['time']	=	date('Y-m-d H:i:s');
				$insert['type']	=	$shop['type'];
				// 判断收货地址是否为空 20201211 libra
				if ($shop['type'] == 0) {
					$insert['dizhi'] = $req['shdz'];
				}

				$dizhi = "";
				if (!empty($req['shdz'])) {
					$dizhi = $req['shdz'];
				}
				//$insert['dizhi']	=	input('shdz');
				//$insert['picurl']	=	$shop['picurl'];
				$insert['title']	=	$shop['title'];
				$insert['paytype'] = $paytype;

				$fahuoxinxi = "0";
				if ($shop['type'] != 0) {
					$fahuoxinxi = "1";
				}

				$dingdan = $shopdingdanModel->save(['uid' => $userid, 'username' => $usernamereq, 'oderid' => $orderid, 'pay_jiage' => $shop['jifen'], 'spid' => $pid, 'time' => date('Y-m-d H:i:s'), 'type' => $shop['type'], 'title' => $shop['title'], 'paytype' => $paytype, 'dizhi' => $dizhi, 'fahuoxinxi' => $fahuoxinxi]);

				$item = '';
				if ($shop['type'] == 0) {
					$item = '购买商品';
				} else {
					$item = '购买会员卡';

					$cardid = $shop['card_id'];
					$cardtime = Cardmodel::where('id', $cardid)->value('time');
					$userviptime = Usermodel::where('id', $userid)->value('vip_time');
					/*libra 重置VIP购买时间*/
					$viptime = strtotime($userviptime);
					if (time() > $viptime) {
						$viptime = time() + $cardtime * 24 * 60 * 60;
					} else {
						$viptime = $viptime + $cardtime * 24 * 60 * 60;
					}
					//$addviptime = strtotime($userviptime) + $cardtime * 60 * 60 * 24;
					Usermodel::where('id', $userid)->update(['vip_time' => date('Y-m-d H:i:s', $viptime)]);
					//Usermodel::where('id', $userid)->update(['vip_time' => date('Y-m-d H:i:s', $addviptime)]);
				}
				$jifenlogModel = new JifenlogModel();
				$jifenlogModel->save(['uid' => $userid, 'username' => $usernamereq, 'item' => $item, 'jifen' => $shop['jifen'], 'ctime' => time()]);

				if ($dingdan) {
					return json(['code' => 1, 'msg' => '恭喜您，兑换成功!']);
					die;
				}
			}
		} else {
			$shengyu_money = $username['money'] - $shop['yue'];
			if (Usermodel::where('id', $userid)->update(['money' => $shengyu_money])) {
				$insert['uid']	=	$userid;
				$insert['username']	=	$usernamereq;
				$orderid =   date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
				$insert['orderid']  = $orderid;

				$insert['payjiage']  = $shop['yue'];
				$insert['spid']	=	$pid;
				$insert['time']	=	date('Y-m-d H:i:s');
				$insert['type']	=	$shop['type'];
				// 判断收货地址是否为空 20201211 libra
				$dizhi = "";
				if (!empty($req['shdz'])) {
					$dizhi = $req['shdz'];
				}

				if ($shop['type'] == 0) {
					$insert['dizhi'] = $req['shdz'];
				}

				//$insert['dizhi']	=	input('shdz');
				//$insert['picurl']	=	$shop['picurl'];
				$insert['title']	=	$shop['title'];
				$insert['paytype'] = $paytype;


				$fahuoxinxi = "0";
				if ($shop['type'] != 0) {
					$fahuoxinxi = "1";
				}

				$dingdan = $shopdingdanModel->save(['uid' => $userid, 'username' => $usernamereq, 'oderid' => $orderid, 'pay_jiage' => $shop['yue'], 'spid' => $pid, 'time' => date('Y-m-d H:i:s'), 'type' => $shop['type'], 'title' => $shop['title'], 'paytype' => $paytype, 'dizhi' => $dizhi, 'fahuoxinxi' => $fahuoxinxi]);

				$item = '';
				if ($shop['type'] == 0) {
					$item = '购买商品';
				} else {
					$item = '购买会员卡';

					$cardid = $shop['card_id'];
					$cardtime = Cardmodel::where('id', $cardid)->value('time');
					$userviptime = Usermodel::where('id', $userid)->value('vip_time');
					//$addviptime = strtotime($userviptime) + $cardtime * 60 * 60 * 24;
					/*libra 重置VIP购买时间*/
					$viptime = strtotime($userviptime);
					if (time() > $viptime) {
						$viptime = time() + $cardtime * 24 * 60 * 60;
					} else {
						$viptime = $viptime + $cardtime * 24 * 60 * 60;
					}
					Usermodel::where('id', $userid)->update(['vip_time' => date('Y-m-d H:i:s', $viptime)]);
					//Usermodel::where('id', $userid)->update(['vip_time' => date('Y-m-d H:i:s', $addviptime)]);
				}
				$moneylogModel = new MoneylogModel();
				$moneylogModel->save(['uid' => $userid, 'username' => $usernamereq, 'item' => $item, 'cid' => $userid, 'c_username' => $usernamereq, 'money' => $shop['yue'], 'ctime' => time()]);

				if ($dingdan) {
					return json(['code' => 1, 'msg' => '购买成功!']);
					die;
				}
			}
		}
	}

	public function uservip($userid, $cardid)
	{
		$cardtime = Cardmodel::where('id', $cardid)->value('time');
		$userviptime = Usermodel::where('id', $userid)->value('vip_time');
		$addviptime = strtotime($userviptime) + $cardtime * 60 * 60 * 24;
		Usermodel::where('id', $userid)->update(['vip_time' => date('Y-m-d H:i:s', $addviptime)]);
	}


	//转账
	public function zhuanzhang(Request $request)
	{
		$req = $request->post();

		$userid = $req['userid'];
		$username = $req['username'];
		$daili_username = $req['daili_username'];
		$yaoqingma = $req['yaoqingma'];
		$jine = $req['jine'];
		$daili_userid = 0;
		$daili_has_money = 0;

		$has_money = Usermodel::where('id', $userid)->value('money');
		if ($has_money < $jine) {
			$this->success('余额不足,请及时充值', '', 200);
			return;
		}

		$moneylogModel = new MoneylogModel();
		if ($daili_username) {
			$daili_userid = Usermodel::where('username', $daili_username)->value('id');
			$daili_has_money = Usermodel::where('username', $daili_username)->value('money');
		} else {
			$daili_userid = Usermodel::where('number', $yaoqingma)->value('id');
			$daili_has_money = Usermodel::where('number', $yaoqingma)->value('money');
		}

		if (!$daili_userid) {
			$this->success('该代理不存在', '', 200);
			return;
		} else {
			if ($daili_userid == $userid) {
				$this->success('不能转给自己', '', 200);
				return;
			}

			$has_money = $has_money - $jine;
			Usermodel::where('id', $userid)->update(['money' => $has_money]);

			$daili_has_money = $daili_has_money + $jine;
			Usermodel::where('id', $daili_userid)->update(['money' => $daili_has_money]);
			//$this->success('转账成123功','',200);
			$res = $moneylogModel->save(['uid' => $userid, 'username' => $username, 'item' => '代理转账', 'cid' => $daili_userid, 'c_username' => $daili_username, 'money' => $jine, 'ctime' => time()]);
			if ($res) {
				$this->success('转账成功', '', 200);
			} else {
				$this->error('网络错误', '', 100);
			}
		}
	}
}

<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Category_EweiShopV2Page extends MobilePage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$merchid = intval($_GPC['merchid']);

		$sql = "select * from ims_ewei_shop_category where level=1 order by id";

		$getCategory = pdo_fetchall($sql);

		include $this->template();
	}

	public function getGoods(){

		$id = $_POST['id'];

        $sql = "select id,title,salesreal,productprice,marketprice,total,thumb from ims_ewei_shop_goods where pcate=" . $id;

		$getGoods = pdo_fetchall($sql);
//var_dump($getGoods);exit;
        // 商品库存重新计算
        foreach($getGoods as $k => $v) {
            $agenttotal = pdo_fetchcolumn('select sum(vstock) from ' . tablename('ewei_shop_agent_stock') . ' where goodsid=' . $v['id'] . ' and optionid=0');
            $getGoods[$k]['total'] = intval($getGoods[$k]['total']) + intval($agenttotal);
        }

		echo json_encode($getGoods);

	}

	public function getIstime(){

		$sql = "select id,title,content,productprice,marketprice,total,thumb from ims_ewei_shop_goods where istime=1";

		$getIstime = pdo_fetchall($sql);

		echo json_encode($getIstime);

	}

	public function getIsnew1(){

		$sql = "select id,title,productprice,marketprice,total,thumb from ims_ewei_shop_goods where isnew=1 limit 2";

		$getIsnew1 = pdo_fetchall($sql);

		echo json_encode($getIsnew1);

	}

	public function getIsnew2(){

		$sql = "select id,title,productprice,marketprice,total,thumb from ims_ewei_shop_goods where isnew=1 order by id desc limit 2";

		$getIsnew2 = pdo_fetchall($sql);

		echo json_encode($getIsnew2);

	}


}


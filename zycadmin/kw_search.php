<?php
/**
 * 关键词排名查询1
 */
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/datalistcp.class.php");
//获取站长key和域名
$row = $dsql->GetOne("Select value From `#@__sysconfig` where varname='zz_key'; ");
$row1 = $dsql->GetOne("Select value From `#@__sysconfig` where varname='domain_name'; ");
$zz_key =  $row['value'];
$domain_name =  $row1['value'];
if(!$zz_key){
    ShowMsg("请先设置站长工具接口key！","-1");
    exit();
}
if(!$domain_name){
    ShowMsg("请先设置好要查询的域名！","-1");
    exit();
}
if(empty($dopost)) $dopost = '';
if($dopost=='save'){
    $old_task = $dsql->GetOne("Select task_id,status,add_time From `#@__keywords_task` ORDER BY aid DESC limit 1 ; ");
    /*if(time() - $old_task['add_time'] < 7*24*3600){
        echo -1;
        exit();
    }*/
    $type_arr = [
        ['type'=>1,'url'=>'http://apidata.chinaz.com/BatchAPI/BaiduPcRanking'],
        ['type'=>2,'url'=>'http://apidata.chinaz.com/BatchAPI/BaiduMobileRanking'],
        ['type'=>3,'url'=>'http://apidata.chinaz.com/BatchAPI/SoPcRanking'],
        ['type'=>4,'url'=>'http://apidata.chinaz.com/BatchAPI/SoMobileRanking'],
        ['type'=>5,'url'=>'http://apidata.chinaz.com/BatchAPI/SogouPcRanking'],
        ['type'=>6,'url'=>'http://apidata.chinaz.com/BatchAPI/SogouMobileRanking'],
    ];
    $keywords_count = count($keywords);
    $i = ceil($keywords_count/50);//循环次数
    for($x = 0; $x < $i; $x ++){
        $data = [
            'key' => $zz_key,
            'domainName' => $domain_name,
            'keywords' => implode('|',array_slice($keywords,$x * 50,50)),
        ];
        foreach($type_arr as $k=>$v){
            $post_data = json_encode($data);
            $res  = json_decode(request_post($v['url'],$post_data),true);
            $type = $v['type'];
            if($res['StateCode'] == 1){
                $task_id = $res['TaskID'];
                $time = time();
                $query = "INSERT INTO `#@__keywords_task`(task_id,`type`,add_time) Values('$task_id','$type','$time')";
                $dsql->ExecuteNoneQuery($query);
            }else{
                echo 0;exit;
            }
            if(!$res){

            }
        }
    }
    echo 1;
}else{
    $query = "select * from #@__keywords_rank group by keyword";
    $dlist = new DataListCP();
    $dlist->SetTemplet(DEDEADMIN."/templets/kw_search.htm");
    $dlist->SetSource($query);
    $dlist->display();
}

/**
 * 模拟post提交
 */
function request_post($url, $post_data) {
    $curlPost = $post_data;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS,$curlPost);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($curlPost))
    );

    $result = curl_exec($ch);
    return $result;
}
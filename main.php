<?php
header("Access-Control-Allow-Origin:*");
header('Content-Type:application/json; charset=utf-8');
function get_qq_nick($qqCode) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://users.qzone.qq.com/fcg-bin/cgi_get_portrait.fcg?uins=' . $qqCode);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($curl, CURLOPT_POSTFIELDS, '------WebKitFormBoundaryYTwvlk5brGmyD3Mn');
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: multipart/form-data; boundary=---011000010111000001101001',
    ]);
    $response = curl_exec($curl);
    $encode = mb_detect_encoding($response, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
    $response = mb_convert_encoding($response, 'UTF-8', $encode);
    $name = json_decode(substr($response, 17, -1), true);
    curl_close($curl);
    $avatar = 'https://q2.qlogo.cn/headimg_dl?dst_uin=' . $qqCode . '&spec=640';
    $results = array(
        'qq' => $qqCode,
        'name' => urldecode($name[$qqCode][6] ?? ''),
        'email' => $qqCode . '@qq.com',
        'avatar' => str_replace('http://', 'https://', $avatar),
    );
    return $results;
}

function isValidQQ($qq) {
    $pattern = '/^[1-9]\d{4,9}$/';
    return preg_match($pattern, $qq) === 1;
}

// 传参
$qq = isset($_REQUEST['qq']) ? $_REQUEST['qq'] : "";
// 检查是否提供 qq 参数
if (!$qq) {
    $response['success'] = false;
    $response['msg'] = '缺少必要参数！';
    $response['data'] = null;
} elseif (!isValidQQ($qq)) {
    $response['success'] = false;
    $response['msg'] = '查询的QQ格式不正确！';
    $response['data'] = null;
} else {
    $response['success'] = true;
    $response['msg'] = '查询成功！';
    $response['data'] = get_qq_nick($qq);
    $response['time'] = date('Y-m-d H:i:s');
    $response['api_vers'] = 'qqinfo_v5';
}
echo json_encode($response,320);
?>

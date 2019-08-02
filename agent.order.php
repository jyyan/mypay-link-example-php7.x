<?php
// (1)付費請求
// 以下範例為經銷商用(PHP)
// 經銷商商務代號
$agentUid = "A1111111111001";
// 經銷商商店金鑰
$key = "Xd668CSjnXQLD26Hia8vapkOgGXAv68s";
// 商品資料
$payment = array();
$payment['store_uid'] = "123456790002"; //特店id
$payment['item'] = 1;
$payment['i_0_id'] = '0886449';
$payment['i_0_name'] = '商品名稱';
$payment['i_0_cost'] = '10';
$payment['i_0_amount'] = '1';
$payment['i_0_total'] = '10';
$payment['cost'] = 10;
$payment['user_id'] = "phper";
$payment['order_id'] = "1234567890";
$payment['ip'] = $_SERVER['REMOTE_ADDR']; // 此為消費者IP,會做為驗證用
$payment['pfn'] = "all";

//加密方法 for PHP 7.x 使用 openssl
function encrypt($fields, $key)
{
  $data = json_encode($fields);
  $iv = random_bytes(16);
  $padding = 16 - (strlen($data) % 16);
  $data .= str_repeat(chr($padding), $padding);
  $data = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv);
  $data = base64_encode($iv . $data);
  return $data;
}


//送出欄位
$postData = array();
$postData['agent_uid'] = $agentUid;
$postData['service'] = encrypt(array(
  'service_name' => 'api',
  'cmd' => 'api/orders'
), $key);
$postData['encry_data'] = encrypt($payment, $key);

//資料送出
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, "https://pay.usecase.cc/api/agent");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
$result = curl_exec($ch);
curl_close($ch);
// 回傳 JSON 內容
$result = json_decode($result);

//​ 由經銷商決定是否立即導轉至付款頁面,導轉前可自行決定是否存取json資訊
if ($result->code == "200"){
  header("Location: " . $result->url);
}else{
  print($result->msg);
  return false;
}



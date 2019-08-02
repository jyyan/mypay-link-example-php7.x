<?php
// (2)交易查詢
// 以下範例為特約商店用(PHP)
// 特約商店商務代號
$storeUid = "398800730001";
// 特約商店金鑰
$key = "hSVkUjvkcKqzn4FF9BFHLarhV9puQmAV";
// 商品資料
$order = array();
$order['uid'] = "25160";
$order['key'] = "4d706668d98c26e11bae827be7e7efcd";

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

// 送出欄位
$postData = array();
$postData['store_uid'] = $storeUid;
$postData['service'] = encrypt(array(
'service_name' => 'api',
'cmd' => 'api/queryorder'
), $key);
$postData['encry_data'] = encrypt($order, $key);
// 資料送出
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, "https://pay.usecase.cc/api/init");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
$result = curl_exec($ch);
curl_close($ch);
// 回傳 JSON 內容
print_R($result);

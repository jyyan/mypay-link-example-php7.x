<?php
// (3)取消定期定額式扣款
// 以下範例為特約商用(PHP)
// 特約商店商務代號
$storeUid = "398800730001";
// 特約商店金鑰
$key = "QzanfybA3BEZejhUfLic4AqbbQSJx8nb";
// 定期定額式扣款取消資料
$order = array();
$order['store_uid'] = $storeUid;
$order['order_id'] = "B5BA6ECF-7396-4635-B57A-891934A12519";
$order['group_id'] = "D0000000109";
$order['stop_time'] = "20170520";
$order['stop_reason'] = "消費者不喜翻~";

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
'cmd' => 'api/disabledirectdebit'
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

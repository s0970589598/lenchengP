<?php

namespace App\Http\Controllers;

use Parsedown;
use File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\Yaml\Yaml;

class GooglemapController extends Controller
{
    public function getMapDetail()
    {
        // 你的 API 金鑰
        $apiKey = 'AIzaSyBxslMBi_SA5NHGGxRCvQrXuuBxbnQsIPA';

        // Place ID
        $placeId = 'ChIJTccU9Ts9aTQRo9ZizRTNjDE';

        // Google Maps Places API 的 URL
        $apiUrl = 'https://maps.googleapis.com/maps/api/place/details/json';

        // 設定語言為 zh-Hant-TW
        $language = 'zh-Hant-TW';

        // 組合 URL
        $url = $apiUrl . '?key=' . $apiKey . '&placeid=' . $placeId  . '&language=' . $language;;

        // 初始化 cURL
        $ch = curl_init();

        // 設定 cURL 選項
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 執行 cURL，取得 API 回應
        $response = curl_exec($ch);

        // 關閉 cURL 資源，釋放資源
        curl_close($ch);

        // 將 API 回應發送給前端
        return $response;
    }


}

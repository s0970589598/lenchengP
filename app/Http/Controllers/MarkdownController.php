<?php

namespace App\Http\Controllers;

use Parsedown;
use File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Log;
class MarkdownController extends Controller
{
    public function convert($source,$file)
    {
        $mdFilePath = '/var/www/lenchengP/content/collections/' . $source . '/' . $file;

        if (!file_exists($mdFilePath)) {
            abort(404);
        }

        $mdContent = File::get($mdFilePath);

          // 解析 Markdown 資料
          $parsedData = $this->parseMarkdown($mdContent, "detail", $source);

          // 回傳 JSON 格式的資料
          return response()->json(['data' => $parsedData]);

    }

    private function parseMarkdown($markdownContent, $type="list", $source)
    {
        $parsedown = new Parsedown();
            // 分離 YAML 和 Markdown 部分

        list($yamlData, $markdownText) = explode('---', $markdownContent, 3);
        // Log::info($markdownText);
        $markdownArray = Yaml::parse($markdownText);
        if ($type != "list") {
            return $markdownArray;
        }
        switch ($source) {
            case 'articles':
          $selectedData = [
                    'id' => $markdownArray['id'] ?? null,
                    'title' => $markdownArray['title'] ?? null,
                    'author' => $markdownArray['author'] ?? null,
                    'feature_pic' => $markdownArray['feature_pic'] ?? null,
                    'bard_field_fir' => isset($markdownArray['bard_field'][0]) ? [$markdownArray['bard_field'][0]] : [],
                    'bard_field_sec' => isset($markdownArray['bard_field'][1]) ? [$markdownArray['bard_field'][1]] : [],
                    'taggable_field' => isset($markdownArray['taggable_field']) ? [$markdownArray['taggable_field']] : [],

                ];

                break;
            case 'news':
                $selectedData = [
                    'id' => $markdownArray['id'] ?? null,
                    'source_type' => $markdownArray['source_type'] ?? null,
                    'essay' => $markdownArray['essay'] ?? null,
                    'title' => $markdownArray['title'] ?? null,
                    'feature_pic' => $markdownArray['feature_pic'] ?? null,
                    'bard_field_fir' => isset($markdownArray['bard_field'][0]) ? [$markdownArray['bard_field'][0]] : [],
                    'bard_field_sec' => isset($markdownArray['bard_field'][1]) ? [$markdownArray['bard_field'][1]] : [],
                    'taggable_field' => isset($markdownArray['taggable_field']) ? [$markdownArray['taggable_field']] : [],
                    'start_date' => isset($markdownArray['start_date']) ? [$markdownArray['start_date']] : [],
                    'end_date' => isset($markdownArray['end_date']) ? [$markdownArray['end_date']] : [],
                    'url_field' => isset($markdownArray['url_field']) ? [$markdownArray['url_field']] : [],
                ];
                break;
            case 'service':
                $selectedData = [
                    'id' => $markdownArray['id'] ?? null,
                    'service_type' => $markdownArray['service_type'] ?? null,
                    'source_type' => $markdownArray['source_type'] ?? null,
                    'essay' => $markdownArray['essay'] ?? null,
                    'title' => $markdownArray['title'] ?? null,
                    'feature_pic' => $markdownArray['feature_pic'] ?? null,
                    'bard_field_fir' => isset($markdownArray['bard_field'][0]) ? [$markdownArray['bard_field'][0]] : [],
                    'bard_field_sec' => isset($markdownArray['bard_field'][1]) ? [$markdownArray['bard_field'][1]] : [],
                    'taggable_field' => isset($markdownArray['taggable_field']) ? [$markdownArray['taggable_field']] : [],
                    'class_week_date' => isset($markdownArray['class_week_date']) ? [$markdownArray['class_week_date']] : [],
                    'teacher' => isset($markdownArray['teacher']) ? [$markdownArray['teacher']] : [],
                ];
                break;
            default:
                return [];

        }
        return $selectedData;
    }

    public function getMarkdownFilesInDirectory($file)
    {
        $markdownFiles = [];
        $directory = '/var/www/lenchengP/content/collections/' . $file . '/';
        $sortBy = 'date';
        $page = 1;
        $perPage = 10;
        $sourceTypeParam = 'groupclass';
        // 檢查目錄是否存在
        if (File::exists($directory)) {
            // 取得目錄內的所有檔案
            $files = File::files($directory);



            // 排序
            $sortedFiles = $this->sortFiles($files, $sortBy);

            // 分頁
            $pagedFiles = array_slice($sortedFiles, ($page - 1) * $perPage, $perPage);

            // 總檔案數
            $totalFiles = count($sortedFiles);

            // 計算總頁數
            $totalPages = ceil($totalFiles / $perPage);

            // 分頁
            $pagedFiles = array_slice($sortedFiles, ($page - 1) * $perPage, $perPage);

            foreach ($pagedFiles as $file) {
                // 檢查檔案是否是 .md 檔
                if ($file->getExtension() === 'md') {
                    // 取得檔案的檔名
                    $fileName = $file->getFilenameWithoutExtension();
                    $markdownFiles[] = $fileName;
                }
            }
        }

        return [
            'data' => $markdownFiles,
            'pagination' => [
                'total_pages' => $totalPages,
                'current_page' => $page,
            ],
        ];
    }

    private function sortFiles($files, $sortBy)
    {
        switch ($sortBy) {
            case 'name':
                // 依檔名排序
                usort($files, function ($a, $b) {
                    return strcmp($a->getFilename(), $b->getFilename());
                });
                break;
            case 'date':
                // 依修改日期排序
                usort($files, function ($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                break;
            // 添加其他排序方式，如果需要的話
        }

        return $files;
    }

    public function getArticleList(Request $request, $source){
        $markdownFiles = [];
        $parsedData = [];
        // $directory = '/var/www/lenchengP/content/collections/articles/';
        $sortBy = 'date';
        $page = 1;
        $perPage = 6;

        $page = $request->input('page', 1); // 獲取 page 參數，默認值為 1
        $sourceTypeParam = $request->input('source-type', 1); // 獲取 page 參數，默認值為 1
        // $type = $request->type;
        $directory = '/var/www/lenchengP/content/collections/'. $source .'/';
        // 檢查目錄是否存在
        if (File::exists($directory)) {
            // 取得目錄內的所有檔案
            $files = File::files($directory);
            $filteredFiles = $files;

            if(isset($sourceTypeParam) && $sourceTypeParam !=1){
                // 假設 $files 是你原始的檔案陣列，每個元素是一個檔案物件
                $filteredFiles = array_filter($files, function($file) use ($sourceTypeParam) {
                    // 取得檔案的檔名（不包含副檔名）
                    $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    
                    if ($sourceTypeParam != 'partner'){
                        return strpos($fileName, 'partner') == false;
                    }

                    return strpos($fileName, $sourceTypeParam) !== false;

                });
            }
            // array_multisort($filesize,SORT_DESC,SORT_NUMERIC, $filteredFiles);//按大小排序          
            //$sortedFiles = array_multisort($filename,SORT_DESC,SORT_STRING, $files);//按名字排序          
            //array_multisort($filetime,SORT_DESC,SORT_STRING, $files);//按時間排序

            // 排序
            $sortedFiles = $this->sortFiles($filteredFiles, $sortBy);

            // 分頁
            $pagedFiles = array_slice($sortedFiles, ($page - 1) * $perPage, $perPage);

            // 總檔案數
            $totalFiles = count($sortedFiles);

            if ($totalFiles == 0){
                $totalPages = 0;
            } else {
                // 計算總頁數
                $totalPages = ceil($totalFiles / $perPage);
            }


            // 分頁
            $pagedFiles = array_slice($sortedFiles, ($page - 1) * $perPage, $perPage);

            foreach ($pagedFiles as $file) {
                // 檢查檔案是否是 .md 檔
                if ($file->getExtension() === 'md') {
                    // 取得檔案的檔名
                    $fileName = $file->getFilenameWithoutExtension();
                    $markdownFiles[] = $fileName;
                    $mdFilePath = '/var/www/lenchengP/content/collections/'. $source .'/' . $fileName . '.md';
                    if (!file_exists($mdFilePath)) {
                        abort(404);
                    }

                    $mdContent = File::get($mdFilePath);

                    // 解析 Markdown 資料
                    $parsedData[] = $this->parseMarkdown($mdContent,'list', $source);


                }
            }



        }

        return [
            'data' => $markdownFiles,
            'article' => $parsedData,
            'pagination' => [
                'total_pages' => $totalPages,
                'current_page' => (int) $page,
            ],
        ];
    }

}

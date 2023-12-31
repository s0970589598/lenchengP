<?php

namespace App\Http\Controllers;

use Parsedown;
use File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\Yaml\Yaml;

class MarkdownController extends Controller
{
    public function convert($file)
    {
        $mdFilePath = '/var/www/html/content/collections/articles/' . $file;

        if (!file_exists($mdFilePath)) {
            abort(404);
        }

        $mdContent = File::get($mdFilePath);

          // 解析 Markdown 資料
          $parsedData = $this->parseMarkdown($mdContent, "detail");

          // 回傳 JSON 格式的資料
          return response()->json(['data' => $parsedData]);

    }

    private function parseMarkdown($markdownContent, $type="list")
    {
        $parsedown = new Parsedown();
            // 分離 YAML 和 Markdown 部分

        list($yamlData, $markdownText) = explode('---', $markdownContent, 3);

        $markdownArray = Yaml::parse($markdownText);
        if ($type == "list") {
            return $selectedData = [
                'id' => $markdownArray['id'] ?? null,
                'title' => $markdownArray['title'] ?? null,
                'author' => $markdownArray['author'] ?? null,
                'feature_pic' => $markdownArray['feature_pic'] ?? null,
                'bard_field_fir' => isset($markdownArray['bard_field'][0]) ? [$markdownArray['bard_field'][0]] : [],
                'bard_field_sec' => isset($markdownArray['bard_field'][1]) ? [$markdownArray['bard_field'][1]] : [],
                'taggable_field' => isset($markdownArray['taggable_field']) ? [$markdownArray['taggable_field']] : [],
            ];
        } else {
            // 選擇需要的屬性
            return $markdownArray;
        }
    }

    public function getMarkdownFilesInDirectory($directory = '/var/www/html/content/collections/articles/', $sortBy = 'name', $page = 1, $perPage = 10)
    {
        $markdownFiles = [];

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

    public function getArticleList(Request $request,$directory = '/var/www/html/content/collections/articles/', $sortBy = 'name', $page = 1, $perPage = 9){
        $markdownFiles = [];
        $parsedData = [];
        $page = $request->input('page', 1); // 獲取 page 參數，默認值為 1
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
                    $mdFilePath = '/var/www/html/content/collections/articles/' . $fileName . '.md';
                    if (!file_exists($mdFilePath)) {
                        abort(404);
                    }

                    $mdContent = File::get($mdFilePath);

                    // 解析 Markdown 資料
                    $parsedData[] = $this->parseMarkdown($mdContent);


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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class YouTubeController extends Controller
{
    public function getPlaylistApi(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'category' => 'nullable|integer',
            'search' => 'nullable|string',
        ]);
        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()]);
        }
        $youtube = $this->getClient();
        $maxResults = 4;
        $regionCode = 'RU';
        if (empty($request['category']) && empty($request['search'])) {
            $options = ['chart' => 'mostPopular', 'maxResults' => $maxResults, 'regionCode' => $regionCode];
            $videos = $youtube->videos->listVideos('snippet', $options);
        } else {
            $options = [
                'maxResults' => $maxResults, //Количество результатов
                'q' => $request['search'], //поисковый запрос
                'regionCode' => $regionCode, //указываем код страны
                'type' => 'video', //Обязательно указываем что ищем видео, т.к. используем videoCategoryId
                'videoCategoryId' => $request['category'] //Ищем по категории если указана
            ];
            $videos = $youtube->search->listSearch("snippet", $options);
        }

        return response()->json($videos);
    }

    public function getCategories(): JsonResponse
    {
        $youtube = $this->getClient();
        $regionCode = 'RU';
        $categories = $youtube->videoCategories->listVideoCategories('snippet', ['regionCode' => $regionCode])->getItems();
        return response()->json($categories);
    }

    private function getClient() {
        return app::make('youtube');
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\QueryException;
use App\Models\Verse;
use App\Models\Chapter;

class QuranController extends Controller
{

  public function index()
  {
    $fetch = Chapter::all();
    $data = [];

    foreach ($fetch as $get) {  
      $push = [
        'number' => $get->number,
        'sequence' => $get->sequence,
        'totalVerse' => $get->verses->count(),
        'name' => [
          'short' => $get->short_name,
          'long' => $get->long_name,
          'transliteration' => $get->transliteration,
          'translation' => $get->translation,
        ],
        'revelation' => $get->revelation,
        'tafsir' => $get->tafsir
      ]; 
      array_push($data, $push);
    }

    return response()->json([
      'code' => 200,
      'status' => 'OK',
      'message' => 'Success fetching all surah',
      'data' => $data
    ], Response::HTTP_OK);
  }


  public function show($id)
  {
    $get = Chapter::find($id);

    if ($get){
      $verses = [];

      foreach ($get->verses as $key) {
        $push = [
          'number' => [
            'inQuran' => $key->inQuran,
            'inSurah' => $key->inSurah
          ],
          'text' => [
            'arabic' => $key->arabic,
            'transliteration' => $key->transliteration
          ],
          'translation' => $key->translation,
          'audio' => $key->audio,
          'tafsir' => [
            'short' => $key->short_tafsir,
            'long' => $key->long_tafsir
          ]
        ];
        array_push($verses, $push);
      }

      return response()->json([
        'conde' => 200,
        'status' => 'OK',
        'message' => 'Success fetching surah',
        'data' => [
          'number' => $get->number,
          'sequence' => $get->sequence,
          'totalVerse' => $get->verses->count(),
          'name' => [
            'short' => $get->short_name,
            'long' => $get->long_name,
            'transliteration' => $get->transliteration,
            'translation' => $get->translation,
          ],
          'revelation' => $get->revelation,
          'tafsir' => $get->tafsir,
          'verses' => $verses
        ]
      ], Response::HTTP_OK);
    }else{
      return response()->json([
        'code' => 404,
        'status' => 'Not Found'
      ], Response::HTTP_NOT_FOUND);
    }
  }



  public function fetch()
  {
    $response = Http::get('https://api.quran.sutanlab.id/surah', null)->body();
    $data = json_decode($response)->data;

    try {
      foreach ($data as $surah) {
        Chapter::create([
          'number' => $surah->number,
          'sequence' => $surah->sequence,
          'short_name' => $surah->name->short,
          'long_name' => $surah->name->long,
          'transliteration' => $surah->name->transliteration->id,
          'translation' => $surah->name->translation->id,
          'revelation' => $surah->revelation->id,
          'tafsir' => $surah->tafsir->id
        ]);
      } 
      return response()->json([
        'code' => 200,
        'status' => 'OK',
        'message' => 'Storing All Data Successfully',
        'total' => Chapter::count()
      ], Response::HTTP_OK);

    } catch (QueryException $e) {
      return response()->json([
        'code' => 422,
        'status' => 'Unprocessable Entity',
        'message' => 'Failed to Store Data : '.$e->errorInfo[2],
        'in' => $surah->name->transliteration->id
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
  }

}

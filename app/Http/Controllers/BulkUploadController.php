<?php

namespace App\Http\Controllers;

use App\Models\Mood;
use App\Models\Song;
use Exception;
use Illuminate\Http\Request;

class BulkUploadController extends Controller
{
    public function bulkUploadSongs(Request $request){
        // dd($request);
        try {
            $filePath= $request->file('import');
            $file = fopen($filePath, 'r');
            // dd($file , $filePath );
            if (!$file) {
                throw new Exception('Unable to open the CSV file.');
            }
            if($file){
                $header = fgetcsv($file);
                $data = [];

                while (($row = fgetcsv($file)) !== false) {
                    $rowData = array_combine($header, $row);
        
                    $data[] = $rowData;
                }

                fclose($file);
                $errorarray = [];
                // dd($data);
                foreach ($data as $row){
                    
                    $mood_id = $this->getMoodId($row['mood']);
                    $singer_id = $this->getSingerId($row['singer']);
                    $album_id = $this->getAlbumId($row['album']);
                    $language_id = $this->getLanguageId($row['language']);
                    $genre_id = $this->getgenreId($row['genre']);
                    $music_director_id = $this->getMusicDirectorId($row['musicDirector']);
                    $releaseDate = date('Y-m-d', strtotime($row['release_date']));


                    Song::create([
                        'title' => $row['title'],
                        'subtitle' => $row['subtitle'],
                        'file' => $row['file'],
                        'image' => $row['image'],
                        'status' => $row['status'],
                        'singer_id' => $singer_id,
                        'album_id' => $album_id,
                        'mood_id' => $mood_id,
                        'language_id' => $language_id,
                        'genre_id' => $genre_id,
                        'music_director_id' => $music_director_id,
                        'year' => $row['year'],
                        'release_date' => $releaseDate,
                        'lyrics' => $row['lyrics'],
                    ]);
                   

                   return response()->json(['success' => 'Songs uploaded successfully.']);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    private function getMoodId($moodName)
    {
        $mood = Mood::where('name', $moodName)->first();
    
        return $mood ? $mood->id : null;
    }
    private function getSingerId($moodName)
    {
        $mood = Mood::where('name', $moodName)->first();
    
        return $mood ? $mood->id : null;
    }
    private function getAlbumId($moodName)
    {
        $mood = Mood::where('name', $moodName)->first();
    
        return $mood ? $mood->id : null;
    }
   
    private function getLanguageId($moodName)
    {
        $mood = Mood::where('name', $moodName)->first();
    
        return $mood ? $mood->id : null;
    }
    private function getgenreId($moodName)
    {
        $mood = Mood::where('name', $moodName)->first();
    
        return $mood ? $mood->id : null;
    }   

    private function getMusicDirectorId($moodName)
    {
        $mood = Mood::where('name', $moodName)->first();
    
        return $mood ? $mood->id : null;
    } 
}




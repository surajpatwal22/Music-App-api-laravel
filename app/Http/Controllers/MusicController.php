<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\AlbumLike;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Like;
use App\Models\Mood;
use App\Models\MusicDirector;
use App\Models\Playlist;
use App\Models\PlaylistsCategory;
use App\Models\Singer;
use App\Models\SingerLike;
use App\Models\Song;
use App\Models\User;
use App\Models\UserSong;
use App\Models\Notification;
use App\Models\SongBanner;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MusicController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'mobile_number' => 'numeric|required_without:email',
            'email' => 'email|required_without:mobile_number',
            'device_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 400,
                'success' => false
            ], 400);
        } else {

            if ($request->has('mobile_number')) {
                $user = User::where('phone', $request->mobile_number)->first();
            } elseif ($request->has('email')) {
                $user = User::where('email', $request->email)->first();
            }

            if (!$user) {
                $newUser = User::create([
                    'phone' => $request->mobile_number,
                    'email' => $request->email,
                    'is_admin' => 0
                ]);

                $token = $newUser->createToken('Personal Access Token', ['expires' => now()->addDays(7)])->plainTextToken;
                $newUser->device_id = $request->device_id;
                $newUser->api_token = $token;
                $newUser->save();

                return response()->json([
                    'message' => 'Registered and logged in successfully',
                    'token' => $token,
                    'status' => 200,
                    'user' => $newUser,
                    'success' => true
                ], 200);
            } else {
                $token = $user->createToken('Personal Access Token', ['expires' => now()->addDays(7)])->plainTextToken;
                $user->device_id = $request->device_id;
                $user->api_token = $token;
                
                $user->save();

                return response()->json([
                    'message' => 'Logged in successfully',
                    'token' => $token,
                    'status' => 200,
                    'user' => $user,
                    'success' => true
                ], 200);
            }
        }
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user) {
            $validator = Validator::make($request->all(), [
                'email' => 'email',
                'mobile_number' => 'min:10|max:10',
                'name' => 'string'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors(),
                    'status' => 400,
                    'success' => false
                ]);
            } else {
                if ($request->photo) {
                    try {
                        if (($request->hasFile('photo'))) {
                            $file = $request->photo;
                            $imageName = $file->getClientOriginalName();
                            $imagePath = public_path() . '/storage/photos/user/';
                            $file->move($imagePath, $imageName);
                            $user->photo = 'public/storage/photos/user/' . $imageName;
                        }
                    } catch (Exception $e) {
                        return $e;
                    }
                }
                if ( $request->email) {
                    $user->email = $request->email;
                }
               
                if ($request->mobile_number) {
                    $user->phone = $request->mobile_number;
                }

                if ($request->name) {
                    $user->name = $request->name;
                }
            
                if ($request->last_name) {
                    $user->last_name = $request->last_name;
                }
                $user->save();

                return response()->json([
                    'message' => 'updated successfully',
                    'status' => 200,
                    'success' => true
                ], 200);
            }

        } else {
            return response()->json([
                'message' => 'user not found',
                'status' => 404,
                'success' => false
            ], 404);
        }
    }

    public function getProfile()
    {
        return response()->json([
            'status' => 200,
            'success' => true,
            'user' => Auth::user()
        ]);
    }

    public function like(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'song_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 400,
                'success' => false
            ], 400);
        } else {

            $Song = Like::where('song_id', $request->song_id)->where('user_id', Auth::user()->id)->first();
            if ($Song) {
                $Song->delete();
                return response()->json([
                    'message' => 'disliked successfully',
                    'status' => 201,
                    'success' => true
                ], 201);
            } else {
                try {
                    $song_create = Like::create([
                        'song_id' => $request->song_id,
                        'user_id' => Auth::user()->id,
                    ]);

                    if ($song_create) {
                        return response()->json([
                            'message' => 'liked successfully',
                            'status' => 201,
                            'success' => true
                        ], 201);
                    } else {
                        return response()->json([
                            'message' => 'something went wrong',
                            'status' => 400,
                            'success' => false
                        ], 400);
                    }
                } catch (Exception $e) {

                    return response()->json([
                        'message' => 'Something went wrong',
                        'status' => 500,
                        'success' => false
                    ], 500);
                }

            }
        }
    }
    

    public function likeAlbum(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'album_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 400,
                'success' => false
            ], 400);
        } else {

            $Album = AlbumLike::where('album_id', $request->album_id)->where('user_id', Auth::user()->id)->first();
            if ($Album) {
                $Album->delete();
                return response()->json([
                    'message' => 'disliked successfully',
                    'status' => 201,
                    'success' => true
                ], 201);
            } else {
                try {
                    $album_create = AlbumLike::create([
                        'album_id' => $request->album_id,
                        'user_id' => Auth::user()->id,
                    ]);

                    if ($album_create) {
                        return response()->json([
                            'message' => 'liked successfully',
                            'status' => 201,
                            'success' => true
                        ], 201);
                    } else {
                        return response()->json([
                            'message' => 'something went wrong',
                            'status' => 400,
                            'success' => false
                        ], 400);
                    }
                } catch (Exception $e) {

                    return response()->json([
                        'message' => 'Something went wrong',
                        'status' => 500,
                        'success' => false
                    ], 500);
                }

            }
        }
    }

    public function likeSinger(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'singer_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 400,
                'success' => false
            ], 400);
        } else {

            $Singer = SingerLike::where('singer_id', $request->singer_id)->where('user_id', Auth::user()->id)->first();
            if ($Singer) {
                $Singer->delete();
                return response()->json([
                    'message' => 'disliked successfully',
                    'status' => 201,
                    'success' => true
                ], 201);
            } else {
                try {
                    $singer_create = SingerLike::create([
                        'singer_id' => $request->singer_id,
                        'user_id' => Auth::user()->id,
                    ]);

                    if ($singer_create) {
                        return response()->json([
                            'message' => 'liked successfully',
                            'status' => 201,
                            'success' => true
                        ], 201);
                    } else {
                        return response()->json([
                            'message' => 'something went wrong',
                            'status' => 400,
                            'success' => false
                        ], 400);
                    }
                } catch (Exception $e) {

                    return response()->json([
                        'message' => 'Something went wrong',
                        'status' => 500,
                        'success' => false
                    ], 500);
                }

            }
        }
    }

    public function getAllMood()
    {
        $mood = Mood::with(['songs'])->orderBy("created_at", "asc")->get();
        return response()->json([
            "success" => true,
            "mood" => $mood,
            "status" => 200
        ], 200);
    }

    public function newRealeaseSongs() {
        $songs = Song::with(['singer', 'mood', 'language', 'genre', 'musicDirector', 'album'])->where('status','active')->orderBy("release_date", "desc")->get();
        return response()->json([
            "success" => true,
            "songs" => $songs
        ], 200);
    }

   
    public function getAllDirector()
    {

        $director = MusicDirector::with(['songs'])->orderBy("created_at", "asc")->get();
        return response()->json([
            "success" => true,
            "directors" => $director
        ], 200);
    }

    public function getDirector($id)
    {
        $director = MusicDirector::with(['songs'])->find($id);
        
        return response()->json([
            "success" => true,
            'status' => 200,
            "director" => $director
        ], 200);
    }

    public function getSongsByMood($mood)
    {
        $moodId = Mood::where('name', $mood)->value('id');
        // dd($moodId);
        if ($moodId == null) {
            return response()->json([
                "Message" => "No mood found with this name",
                "Success" => false,
                'status' => 400
            ], 400);
        } else {
            $songs = Song::where('mood_id', $moodId)->get();

            if (count($songs) <= 0) {
                return response()->json([
                    "Message" => "There are no songs in this mood.",
                    "Success" => true,
                    'status' => 200
                ]);
            }
            return response()->json([
                "Message" => count($songs) . " songs found in the " . $mood . " mood.",
                "Success" => true,
                'status' => 200,
                'songs' => $songs
            ]);

        }
    }

    public function getSongsByLanguage($language)
    {
        $languageName = Language::where('name', $language)->first();
        // dd($moodId);
        if ($languageName == null) {
            return response()->json([
                "Message" => "No Language found with this name",
                "Success" => true,
                'status' => 200
            ], 200);
        } else {
            $songs = Song::where('language_id', $languageName->id)->orderBy("created_at", "desc")->get();

            if (count($songs) <= 0) {
                return response()->json([
                    "Message" => "There are no songs in this language.",
                    "Success" => true,
                    'status' => 200
                ]);
            }
            return response()->json([
                "Message" => count($songs) . " songs found in the " . $language . " language.",
                "Success" => true,
                'status' => 200,
                'songs' => $songs
            ]);

        }
    }
    public function getSongsByGenere($genre)
    {
        $genereName = Genre::where('name', $genre)->first();
        if ($genereName == null) {
            return response()->json([
                "Message" => "No Genere found with this name",
                "Success" => false,
                'status' => 400
            ], 400);
        } else {
            $songs = Song::where('genre_id', $genereName->id)->orderBy("created_at", "desc")->get();

            if (count($songs) <= 0) {
                return response()->json([
                    "Message" => "There are no songs in this language.",
                    "Success" => false,
                    'status' => 404
                ]);
            }
            return response()->json([
                "Message" => count($songs) . " songs found in the " . $genre . " genere.",
                "Success" => true,
                'status' => 200,
                'songs' => $songs
            ]);

        }
    }


    public function getSongsByYear($year)
    {
        $songs = Song::where('year', $year)->get();
        return response()->json([
            'message' => 'These are all the songs from ' . $year . '.',
            'success' => true,
            'status' => 200,
            'songs' => $songs
        ], 200);
    }

    public function trendingSongs(Request $request)
    {
        $startDate = Carbon::now()->subDays(7);

        $trendingSongs = UserSong::select('song_id', DB::raw('count(*) as total'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('song_id')
            ->orderByDesc('total')
            ->get();
        $data=[];
        foreach ($trendingSongs as $trendingSong) {
            $song = Song::where('id', $trendingSong->song_id)->first();
            if($song != null){
                array_push($data,$song);
            }
            // $trendingSong->setAttribute('song_details', $song);
        }
        return response()->json([
            'message' => 'Trending songs retrieved successfully',
            'status' => 200,
            'success' => true,
            'trendingSongs' => $data
        ], 200);
    }

    public function trendingDaily(Request $request)
    {
        $startDate = Carbon::now()->subDays(1);

        $trendingSongs = UserSong::select('song_id', DB::raw('count(*) as total'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('song_id')
            ->orderByDesc('total')
            ->get();
            $data=[];
        foreach ($trendingSongs as $trendingSong) {
            $song = Song::where('id', $trendingSong->song_id)->first();
            if($song != null){
                array_push($data,$song);
            }
            // $trendingSong->setAttribute('song_details', $song);
        }
        return response()->json([
            'message' => ' Daily Trending songs retrieved successfully',
            'status' => 200,
            'success' => true,
            'trendingSongs' => $data
        ], 200);
    }

    public function superhitSongs(Request $request)
    {
        try {
            $superhitSongs = UserSong::select('song_id', DB::raw('count(*) as total'))
                ->groupBy('song_id')
                ->orderByDesc('total')
                ->get();
            $data =[];
            foreach ($superhitSongs as $superhitsong) {
                $song = Song::where('id', $superhitsong->song_id)->first();
                if($song != null){
                    array_push($data,$song);
                }
                // $superhitsong->setAttribute('song_details', $song);
            }
            return response()->json([
                'message' => 'Superhit songs retrieved successfully',
                'status' => 200,
                'success' => true,
                'superhitsongs' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error retrieving superhit songs: ' . $e->getMessage(),
                'status' => 500,
                'success' => false
            ], 500);
        }

    }

    public function trendingAlbums(Request $request)
    {
        $startDate = Carbon::now()->subDays(7);

        $trendingAlbums1 = UserSong::join('songs', 'user_songs.song_id', '=', 'songs.id')
            ->select('user_songs.song_id', 'user_songs.created_at', 'songs.album_id')
            ->where('user_songs.created_at', '>=', $startDate)
            ->get()->groupBy('album_id');
        // return $trendingAlbums;

        $trendingAlbums = [];
        foreach ($trendingAlbums1 as $key => $val) {
            $album = Album::where('id', $key)->first();
            array_push($trendingAlbums ,$album );
        }
        return response()->json([
            'message' => 'Trending albums retrieved successfully',
            'status' => 200,
            'success' => true,
            'trendingAlbums' => $trendingAlbums
        ], 200);
    }

    public function superhitAlbums(Request $request)
    {

        $superhitAlbums1 = UserSong::join('songs', 'user_songs.song_id', '=', 'songs.id')
            ->select('user_songs.song_id', 'user_songs.created_at', 'songs.album_id')
            ->get()->groupBy('album_id');
        // return $trendingAlbums;

        $superhitAlbums = [];
        foreach ($superhitAlbums1 as $key => $val) {
            $album = Album::where('id', $key)->first();
            $count = count($val);
            array_push($superhitAlbums, $album);
        }
        return response()->json([
            'message' => 'Superhit albums retrieved successfully',
            'status' => 200,
            'success' => true,
            'superhitAlbums' => $superhitAlbums
        ], 200);
    }
    public function bollywoodAlbum(Request $request, $genre_id)
    {
        $bollywoodAlbum = Album::join('songs', 'songs.album_id', '=', 'albums.id')->join('genres', 'songs.genre_id', '=', 'genres.id')->select('albums.*')
            ->where('genres.name', 'Bollywood')->get();

        return response()->json([
            'message' => 'Bollywood Album retrieved successfully',
            'status' => 200,
            'success' => true,
            'bollywoodAlbums' => $bollywoodAlbum
        ], 200);
    }
    public function bollywoodAlbumtwo(Request $request)
    {
        $bollywoodAlbums = Album::with('songs')->whereHas('songs', function ($query) {
            $query->whereHas('genre', function ($query) {
                $query->where('name', 'Bollywood');
            });
        })->get();
    
        return response()->json([
            'message' => 'Bollywood Albums and Songs retrieved successfully',
            'status' => 200,
            'success' => true,
            'bollywoodAlbums' => $bollywoodAlbums
        ], 200);
    }
    

    public function getSongsByYearAndLanguage($year, $languageName)
    {
        $songs = Song::where('year', $year)->join('languages', 'songs.language_id', '=', 'languages.id')
            ->where('languages.name', $languageName)
            ->select('songs.*')
            ->get();

        return response()->json([
            'message' => 'These are all the songs from ' . $year . ' in the ' . $languageName . ' language.',
            'success' => true,
            'status' => 200,
            'songs' => $songs
        ], 200);
    }

    public function search(Request $request)
    {
        try {
            $keyword = $request->keyword;
            $songs = Song::where('title', 'like', "%$keyword%")->get();
            $albums = Album::where('name', 'like', "%$keyword%")->get();
            $singers = Singer::where('name', 'like', "%$keyword%")->get();
            $playlists = Playlist::where('playlist_name', 'like', "%$keyword%")->get();

            $message = $keyword ? 'Search results for "' . $keyword . '"' : 'Search results';

            return response()->json([
                'message' => $message ,
                'status' => 200,
                'results' => [
                    'songs' => $songs,
                    'albums' => $albums,
                    'singers' => $singers,
                    'playlists' => $playlists
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400,
            ], 400);
        }
    }

    public function addNotification(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'note' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'error'=> $validator->errors(),
                'status' => 400 ,
                'success' => false
            ]);
        }else{

            if ($request->photo) {
                $file = $request->photo;
                $imageName = $file->getClientOriginalName();
                $imageName = str_replace(' ', '_', $imageName);
                $imagePath = public_path() . '/storage/photos/notification/';
                $file->move($imagePath, $imageName);
            } else {
                return response()->json([
                    'message' => 'Image field is required!',
                    'status' => 400,
                    'success' => false
                ]);
            }
            $notify = Notification::create([
                'note' => $request->note,
                'note_img' => 'public/storage/photos/notification/'. $imageName,
                
            ]);
            if($notify){
                return response()->json([
                    'message'=>'notification created successfully',
                    'status' => 201 ,
                    'success' => true
                ],201);
            }else{
                return response()->json([
                    'message'=> 'something went wrong',
                    'status' => 400 ,
                    'success' => false
                ],400);
            }
        }
    }

    public function getAllNotification()
    {
        $notification = Notification::orderBy("created_at", "asc")->get();
        return response()->json([
            "success" => true,
            "status" => 200 ,
            "notifications" => $notification
        ],200);
    }

    public function getNotification($id){
        $notification = Notification::find($id);
        return response()->json([
            "success" => true,
            "status" => 200,
            "notification" => $notification
        ],200);
    }

    public function downloadSong($id){

        $song = Song::findOrFail($id);

        if(!$song){
            
            return response()->json([
                "message" => "Song not found",
                "success" => false,
                "status" => 404,
            ],404);
        }

        if ($song->status != 'active') {
            return response()->json([
                "message" => "Song is inactive",
                "success" => false,
                "status" => 404,
            ],404);
        }

        $filepath= base_path($song->file);
        $fileName = basename($filepath);
     
        $headers = [
            'Content-Type' => 'audio/mp3',
        ];
    
        return response()->download($filepath,$fileName, $headers);

    }


    public function getMedia(Request $request){
        $id = $request->input('album_id') ?? $request->input('playlist_id') ?? $request->input('artist_id') ?? $request->input('mood_id');
    
        if ($id) {
            if ($request->has('album_id')) {
                $type = 'album';
                $media = Album::with(['songs','songs.singer','songs.album','songs.mood','songs.genre','songs.label','songs.musicDirector'])->find($id);
            } elseif ($request->has('playlist_id')) {
                $type = 'playlist';
                $media = Playlist::with(['songs', 'songs.singer','songs.album','songs.mood','songs.genre','songs.label','songs.musicDirector'])->find($id);
            } elseif ($request->has('artist_id')) {
                $type = 'artist';
                $media = Singer::with(['songs','songs.album','songs.mood','songs.genre','songs.label','songs.musicDirector'])->find($id);
            } elseif ($request->has('mood_id')) {
                $type = 'mood';
                $media = Mood::with(['songs','songs.singer','songs.album','songs.genre','songs.label','songs.musicDirector'])->find($id);
            } else {
                return response()->json(['error' => 'Invalid media type'], 400);
            }
            $songsList = $media->songs;
            $count = $songsList->count();
            $singerIds = collect($songsList)->pluck('singer_id');
    
            $singers = Singer::whereIn('id', $singerIds)->get(['id', 'name']);
           
           
            foreach ($songsList as &$song) {
                $singer = $singers->firstWhere('id', $song['singer_id']);
                if ($singer) {
                    $song['singer_name'] = $singer['name'];
                } else {
                    $song['singer_name'] = 'Unknown';
                }
            }
    
            if ($media) {
                return response()->json([
                    "success" => true,
                    "data" => $media,
                    'count' => $count,
                    "status" => 200,
                ], 200);
            } else {
                return response()->json(['error' => ucfirst($type) . ' not found'], 404);
            }
        } else {
            return response()->json(['error' => 'ID parameter missing'], 400);
        }
    }

    public function userPlaylist(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|unique:playlists,playlist_name',
            'description' => 'required',
            'songs' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 404,
                'success' => false
            ]);
        } else {
            $string = $request->songs;
            $cleanstring = str_replace([ "[", ']' ], "", $string);
            $songs =explode(',',$cleanstring);
            // return $songs;
            $user = Auth::user();

            if ($request->photo) {
                $file = $request->photo;
                $imageName = time() . '.' . $file->getClientOriginalName();
                $imageName = str_replace(' ', '_', $imageName);
                $imagePath = public_path() . '/public/storage/photos/playlist/';
                $file->move($imagePath, $imageName);
                $imageFullPath = 'public/storage/photos/playlist/' . $imageName;
            } 
            else {
                $imageFullPath = null;
            }

            // $notify = Notification::create([
            //     'note' => 'New playlist '.$request->title . 'added by admin',
            //     'status' => 'active',
            //     'note_img' => 'public/storage/photos/notification/' . $imageName,
            // ]);
            
         

            $playlist = Playlist::create([
                'user_id' => $user->id,
                'playlist_name' => $request->name,
                'status' => 'active',
                'description' => $request->description,
                'is_top_chart' => $request->topchart,
                'image' => $imageFullPath
            ]);
        
           
            $playlist1 = Playlist::find($playlist->id);
            foreach ($songs as $song) {
               
             $playlist1->songs()->attach($song);
            }
            // if($notify){
            //     $users = User::where('status','active')->get('device_id');
            //     foreach ($users as $user) {
            //         $token = $user->device_id;
            //         $notification = [
            //             'title' => 'Ring Music',
            //             'body' => $user->name.' added new playlist '.$request->name,
            //         ];
            //         firebasenotification($token,$notification);
            //     }
            // }

            // if($playlist1 && $notify)
            if ($playlist1) {
                return response()->json([
                    'message' => 'playlist created successfully',
                    "success" => true,
                    "playlist" => $playlist,
                    'status' => 200
                ], 200);
            } else {
                return response()->json([
                    'message' => 'playlist not created ',
                    "success" => false,
                    'status' => 404
                ], 404);
            }


        }


    }

    public function getUserPlaylists()
    {
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'message' => 'User not authenticated',
            'success' => false,
            'status' => 401,
        ], 401);
    }
    $playlists = Playlist::with('songs')->where('user_id','=',$user->id)->get();
    // $playlists = $user->playlists()->with('songs')->get();

    return response()->json([
        'message' => 'User playlists fetched successfully',
        'success' => true,
        'playlists' => $playlists,
        'status' => 200,
    ], 200);
    }

    public function addSong(Request $request)
    {
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'message' => 'User not authenticated',
            'success' => false,
            'status' => 401,
        ], 401);
    }
    $playlist_id = $request->playlist_id;
    $song_id = $request->song_id;
    
    $playlist = Playlist::where('id',$playlist_id)->first();
    
    if (!$playlist) {
        return response()->json([
            'message' => 'Playlist not found',
            'success' => false,
            'status' => 404,
        ], 404);
    }
    if ($playlist->songs()->where('song_id', $song_id)->exists()) {
        return response()->json([
            'message' => 'Song already exists in the playlist',
            'success' => false,
            'status' => 422,
        ], 422);
    }
    if($playlist != null){
        if($playlist->user_id = $user->id){
            $playlist->songs()->attach($song_id);

            return response()->json([
                'message' => 'Song added to playlist successfully',
                'success' => true,
                'status' => 200,
            ], 200);
        }
        
    }

  
    }

    public function removeSong(Request $request)
    {
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'message' => 'User not authenticated',
            'success' => false,
            'status' => 401,
        ], 401);
    }
    
    $playlist_id = $request->playlist_id;
    $song_id = $request->song_id;
    
    $playlist = Playlist::where('id',$playlist_id)->first();
    if (!$playlist) {
        return response()->json([
            'message' => 'Playlist not found',
            'success' => false,
            'status' => 404,
        ], 404);
    }

    if (!$playlist->songs()->where('song_id', $song_id)->exists()) {
        return response()->json([
            'message' => 'Song not found in the playlist',
            'success' => false,
            'status' => 404,
        ], 404);
    }

    if($playlist != null){
        if($playlist->user_id = $user->id){
            $playlist->songs()->detach($song_id);

            return response()->json([
                'message' => 'Song removed from playlist successfully',
                'success' => true,
                'status' => 200,
            ], 200);
        }
        
    }
  
    }


    public function getUserLikedSongs()
    {
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'message' => 'User not authenticated',
            'success' => false,
            'status' => 401,
        ], 401);
    }
    $songs = Like::with('song')->where('user_id','=',$user->id)->orderBy("created_at", "desc")->get();

    return response()->json([
        'message' => 'User Liked songs fetched successfully',
        'success' => true,
        'usersongs' => $songs,
        'status' => 200,
    ], 200);
    }

    public function getsongbanner()
    {
        $Banner = SongBanner::where('category','0')->orderBy("created_at", "asc")->get();

        if ($Banner->count() == 0) {
            return response()->json([
                "message" => "No Banners Found",
                "success" => true,
                "status" => 200
            ], 200);
        }

        return response()->json([
            "message" => "Successfully Fetched Banners",
            "success" => true,
            "status" => 200 ,
            "banner" => $Banner
        ],200);
    }

    public function getalbumbanner()
    {
        $Banner = SongBanner::where('category','1')->orderBy("created_at", "asc")->get();

        if ($Banner->count() == 0) {
            return response()->json([
                "message" => "No Banners Found",
                "success" => true,
                "status" => 200
            ], 200);
        }
        
        return response()->json([
            "message" => "Successfully Fetched Banners",
            "success" => true,
            "status" => 200 ,
            "banner" => $Banner
        ],200);
    }

    public function getplaylistCategory(){
        $playlistCat = PlaylistsCategory::with('playlist')->orderBy("created_at", "asc")->get();
        return response()->json([
            "success" => true,
            "status" => 200 ,
            "category" => $playlistCat
        ],200);
    }


}

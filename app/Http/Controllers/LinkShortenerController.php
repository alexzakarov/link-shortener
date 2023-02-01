<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Link;
use Tymon\JWTAuth\Facades\JWTAuth;

class LinkShortenerController extends Controller
{
    public function __construct() {
        $this->middleware('jwt', ['except' => ['store', 'redirect']]);
        $this->middleware('validate', ['except' => ['index', 'show', 'redirect']]);
    }
    public function index(Request $request)
    {
        $user = $request->user();
        return Link::where('user_id', $user->id)->with('userLinks')->get();
    }
 
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $links = Link::where('id', $id)->with(["userLink" => function($q) use ($user){
            $q->where('users.id', '=', $user->id);
        }])->get()->first();

        if ($links && $links->user_id == $user->id) {
            return response()->json([
                "links" => $links,
                "status" => true
            ]);
        }
        return response()->json([
            "message" => "Links couldn't be found",
            "status" => false
        ]);

    }

    public function store(Request $request)
    {
        $user = $request->user();
        $hash = $this->hash();
        if (!$user) {
            Redis::setex($hash, 10, json_encode([
                ...$request->all(), 
                "short_link" => $hash
            ]));
            return Redis::get($hash);
        }

        $link = Link::create([
            'user_id' => $user->id,
            'long_link' => $request->long_link,
            'short_link' => $hash,
        ]);
        return $link;
    }
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $hash = $this->hash();

        $link = Link::where(["id" => $id, "user_id" => $user->id])->update([
            ...$request->all(), 
            "user_id" => $user->id,
            "short_link" => $hash
        ]);
        
        if (!$link) {
            return response()->json([
                "message" => "Link couldn't be found",
                "status" => false
            ], 404);
        }
        return response()->json([
            "message" => "Update is successful",
            "status" => true
        ]);
    }
    public function delete(Request $request, $id)
    {
        $user = $request->user();
        $link = Link::where(["id" => $id, "user_id" => $user->id])->delete();

        if ($link) {
            return response()->json([
                "message" => "link successfully deleted",
                "status" => true
            ]);
        }
        return response()->json([
            "message" => "link coudn't be found",
            "status" => false
        ], 404);
    }

    public function redirect(Request $request, $shortLink)
    {
        $user = $request->user();
        $links = json_decode(Redis::get($shortLink));

        if ($links) {
            return response()->json([
                "link" => $links->long_link,
                "status" => true
            ]);
        }

        $links = Link::where('short_link', $shortLink)->with(["userLink" => function($q) use ($user){
            $q->where('users.id', '=', $user->id);
        }])->get()->first();

        if ($links) {
            return response()->json([
                "link" => $links->long_link,
                "status" => true
            ]);
        } 
        return response()->json([
            "message" => "link coudn't be found",
            "status" => false
        ], 404);

    }

    private function hash() {
        $seed = 'test';
        $string = mb_substr(md5(time().$seed),0,10);
        return $string;
    }


}

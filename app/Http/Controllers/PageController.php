<?php

namespace App\Http\Controllers;

use Str;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function store(Request $request) {
        $title = $request->title;
        $slug = Str::slug($title);

        $toSave = [
            'title' => $title,
            'slug' => $slug,
            'body' => $request->body,
        ];

        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $imageFileName = $image->getClientOriginalName();
            $toSave['featured_image'] = $imageFileName;
            $image->storeAs('public/page_image', $imageFileName);
        }

        $saveData = Page::create($toSave);

        return response()->json(['status' => 200]);
    }
    public function delete(Request $request) {
        $deleteData = Page::where('id', $request->id)->delete();
        return response()->json(['status' => 200]);
    }
    public function update(Request $request) {
        $data = Page::where('id', $request->id);
        $page = $data->first();
        
        $title = $request->title;
        $slug = Str::slug($title);

        $updateData = $data->update([
            'title' => $title,
            'slug' => $slug,
            'body' => $request->body,
        ]);

        return response()->json(['status' => 200]);
    }
    public function all() {
        $pages = Page::orderBy('title', 'ASC')->get(['title', 'slug']);
        return response()->json($pages);
    }
    public function getBySlug($slug) {
        $page = Page::where('slug', $slug)->first();
        return response()->json($page);
    }
}

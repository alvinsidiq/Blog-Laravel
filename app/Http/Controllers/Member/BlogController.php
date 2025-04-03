<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth :: user();
        $search = $request->search;
        

        //dd($user);
        $data = Post::where('user_id',$user->id)->where(function($query)use($search){
            if($search){
                $query->where('title','like',"%{$search}%")->orWhere('content','like',"%{$search}%");
            }
        })->orderBy('id','desc')->paginate(2)->withQueryString();
        // print_r($data);
        return view('member.blogs.index',compact('data'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('member.blogs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request -> validate([
            'title' =>'required',
            'content'=>'required',
            'thumbnail'=>'image|mimes:jpeg,png,jpg|max:10240'

        ],[
            'title.required'=> 'judul wajib di isi',
            'content.required'=> 'content wajib di isi',
            'thumbnail.image'=> 'Hanya Gambar yang di perbolehkan',
            'thumbnail.mimes'=> 'ekstensi yang di perbolehkan hanya jpeg,png,jpg',
            'thumbnail.max'=> 'ukuran maksimum untuk thumbnail adalah 10MB',
        ]);

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $image_name = time()."_". $image->getClientOriginalName();
            $destination_path = public_path(getenv('CUSTOM_THUMBNAIL_LOCATION'));
            $image->move($destination_path,$image_name);
            
        }

        $data = [
            'title'=> $request->title,
            'description'=> $request->description,
            'content'=> $request->content,
            'status'=> $request->status,
            'thumbnail'=> isset($image_name)?$image_name:null,
            'slug'=>$this->generateSlug($request->title),
            'user_id'=>Auth::user()->id
        ];
        
        Post::create($data);

        return redirect()->route('member.blogs.index')->with('success','data berhasil di tambahkan ');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $blog)
    {

        Gate::authorize('edit', $blog);
        //dd($post);
        //print_r($post);
        $data=$blog;
        return view('member.blogs.edit',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $blog)
    {
        // Pengecekan otorisasi
    if ($blog->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

        $request -> validate([
            'title' =>'required',
            'content'=>'required',
            'thumbnail'=>'image|mimes:jpeg,png,jpg|max:10240'

        ],[
            'title.required'=> 'judul wajib di isi',
            'content.required'=> 'content wajib di isi',
            'thumbnail.image'=> 'Hanya Gambar yang di perbolehkan',
            'thumbnail.mimes'=> 'ekstensi yang di perbolehkan hanya jpeg,png,jpg',
            'thumbnail.max'=> 'ukuran maksimum untuk thumbnail adalah 10MB',
        ]);

        if ($request->hasFile('thumbnail')) {
            if (isset($blog->thumbnail) && file_exists(public_path(getenv('CUSTOM_THUMBNAIL_LOCATION'))."/".$blog->thumbnail)) {
               unlink(public_path(getenv('CUSTOM_THUMBNAIL_LOCATION'))."/".$blog->thumbnail);
            }
            $image = $request->file('thumbnail');
            $image_name = time()."_". $image->getClientOriginalName();
            $destination_path = public_path(getenv('CUSTOM_THUMBNAIL_LOCATION'));
            $image->move($destination_path,$image_name);
            
        }

        $data = [
            'title'=> $request->title,
            'description'=> $request->description,
            'content'=> $request->content,
            'status'=> $request->status,
            'thumbnail'=> isset($image_name)?$image_name:$blog->thumbnail,
            'slug'=>$this->generateSlug($request->title,$blog->id)
        ];
        
        Post::where('id',$blog->id)->update($data);

        return redirect()->route('member.blogs.index')->with('success','data berhasil di update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $blog)
    {
        Gate::authorize('delete', $blog);

        if (isset($blog->thumbnail) && file_exists(public_path(getenv('CUSTOM_THUMBNAIL_LOCATION'))."/".$blog->thumbnail)) {
            unlink(public_path(getenv('CUSTOM_THUMBNAIL_LOCATION'))."/".$blog->thumbnail);
         }
        Post :: where('id',$blog->id)->delete();
        return redirect()->route('member.blogs.index')->with('success','Data berhasil di hapus');
    }

    private function generateSlug($title,$id = null){
        $slug = Str :: slug($title);
        $count = Post :: where('slug',$slug)->when($id,function($query,$id){
            return $query->where('id','!=',$id);
        })
        ->count();

        if ($count>0) {
            $slug = $slug."-".($count+1);
            # code...
        }
        return $slug;

    }
}

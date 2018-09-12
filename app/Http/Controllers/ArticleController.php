<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ArticleHomeRequest;
use App\Repositories\ArticleRepository;

class ArticleController extends Controller
{
    protected $article;

    /**
    * @var \App\Repositories\TagRepository
    * @var  \App\Repositories\CategoryRepository
    */
    protected $tag;
    protected $category;

    public function __construct(ArticleRepository $article)
    {
        $this->article = $article;
    }

    /**
     * Display the articles resource.
     *
     * @return mixed
     */
    public function index()
    {
        $articles = $this->article->page(config('blog.article.number'), config('blog.article.sort'), config('blog.article.sortColumn'));

        return view('article.index', compact('articles'));
    }

    /**
     * Display the article resource by article slug.
     *
     * @param  string $slug
     * @return mixed
     */
    public function show($slug)
    {
        $article = $this->article->getBySlug($slug);

        return view('article.show', compact('article'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('article.create');
    }

    /**
     * Store a new article.
     *
     * @param  \App\Http\Requests\ArticleHomeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ArticleHomeRequest $request)
    {
        $data = array_merge($request->all(), [
            'user_id'      => \Auth::id(),
            'last_user_id' => \Auth::id()
        ]);

        $data['is_draft']    = isset($data['is_draft']);
        $data['is_original'] = isset($data['is_original']);
        $data['content'] = $data['content'];
        $this->article->store($data);

        $this->article->syncTag(json_decode($request->get('tags')));

        return redirect()->to('article');
    }

}

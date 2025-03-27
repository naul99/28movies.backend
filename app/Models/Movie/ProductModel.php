<?php

namespace App\Models\Movie;

use App\Models\BackendModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Jobs\ProcessAutoSaveMovie;

class ProductModel extends BackendModel
{
    protected $fillable = ['id'];
    public $crudNotAccepted = ['genre', 'image_poster', 'image_thumb'];    
    public function __construct()
    {
        $this->table = config('constants.TABLE_MOVIE');
        parent::__construct();
    }
    public function listItem($params = null, $options = null)
    {
        if ($options['task'] == "admin-index") {
            $query = self::with('poster');
            if (!empty($params['search'])) {
                $query = $query->where('origin_name', 'like', '%' . $params['search'] . '%');
            }
            $query = $query->orderByDesc('created_at')->paginate(10);
            $this->_data['items'] = $query;
            $this->_data['total'] = $query->total();
        }
        return $this->_data;
    }
    public function getItem($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == 'get-item-info') {
            
            $result = self::with('images', 'genres')->where($this->table . '.id', $params['id'])->first();
        }
        return $result;
    }
    public function saveItem($params = null, $options = null){
        if ($options['task'] == 'add-item') {
           
            $params['insert_id'] = $this->insertGetId($this->prepareParams($params));
            if ($params['insert_id'] && !empty($params['genre'])) {
                $movie = $this->find($params['insert_id']);
                if ($movie) {
                    $movie->genres()->attach($params['genre']);
                }
            }
            if(request()->hasFile('image_poster')){
                    $params['is_thumbnail'] = 0;
                    $this->image_poster     = new ImageModel();
                    $this->image_poster->saveItem($params, ['task' => 'add-item']);
            }
            if(request()->hasFile('image_thumb')){
               
                $params['is_thumbnail'] = 1;
                $this->image_poster     = new ImageModel();
                $this->image_poster->saveItem($params, ['task' => 'add-item']);
            }
            $params['created_at'] = date('Y-m-d H:i:s');
            
            return response()->json(array('success' => true, 'msg' => 'Thêm yêu cầu thành công!'));
        }
        if ($options['task'] == 'edit-item') {
          
            if(request()->hasFile('image_poster')){
                $params['is_thumbnail'] = 0;
                $this->image_poster     = new ImageModel();
                $this->image_poster->saveItem($params, ['task' => 'edit-item']);
            }
            if(request()->hasFile('image_thumb')){
                $params['is_thumbnail'] = 1;
                $this->image_poster     = new ImageModel();
                $this->image_poster->saveItem($params, ['task' => 'edit-item']);
            }
           $params['updated_at'] = date('Y-m-d H:i:s');
           $movie = $this->find($params[$this->primaryKey]);
           $movie->genres()->sync($params['genre']);
           $this->where($this->primaryKey, $params[$this->primaryKey])
                ->update($this->prepareParams($params));
           return response()->json(array('success' => true, 'msg' => 'Cập nhật yêu cầu thành công!'));
        }
        if ($options['task'] == 'change-status') {
            $status = ($params['status'] == "1") ? '0' : '1';
            self::where($this->columnPrimaryKey(), $params[$this->columnPrimaryKey()])->update(['status' => $status]);
        }
        if ($options['task'] == 'add-item-crawler') {
            if (!empty($params['movie_slug'])) {
                $params['url'] = array_map(fn($url) => 'https://ophim1.com/phim/' . $url, $params['movie_slug']);

                foreach ($params['url'] as $url) {
                    ProcessAutoSaveMovie::dispatch($url);
                }  
            }
        }
    }
    public function category(){
        return $this->hasOne(CategoryModel::class,'id', 'category_id');
    }
    public function poster(){
        return $this->hasMany(ImageModel::class,'movie_id', 'id')->where('is_thumbnail', 0);
    }
    public function images(){
        return $this->hasMany(ImageModel::class,'movie_id', 'id');
    }
    public function genres(){
       return $this->belongsToMany(GenreModel::class, config('constants.TABLE_MOVIE_GENRE'), 'movie_id', 'genre_id');
    }
}

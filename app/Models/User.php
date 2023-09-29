<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    // リレーション
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    //数数え
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers', 'favorites']);
    }
    
    
    //フォロー機能
    public function follow($userId)
    {
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            return false;
        } else {
            $this->followings()->attach($userId);
            return true;
        }
    }
    public function unfollow($userId)
    {
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            $this->followings()->detach($userId);
            return true;
        } else {
            return false;
        }
    }
    //現在フォロー中かを確認する
    public function is_following($userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    
    
    
    //フォロー中ユーザーと自分の投稿だけを表示
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    
    
    //お気に入り機能の追加
    //お気に入りする
    public function favorite($favoriteId)
    {
        //exist=同じ
        $exist = $this->is_favorite($favoriteId);
        $its_me = $this->id == $favoriteId;
        
        if ($exist || $its_me) {
            return false;
        } else {
            $this->favorites()->attach($favoriteId);
            return true;
        }
    }
    //お気に入りを外す
    public function unfavorite($favoriteId)
    {
        $exist = $this->is_favorite($favoriteId);
        $its_me = $this->id == $favoriteId;
        
        if ($exist && !$its_me) {
            $this->favorites()->detach($favoriteId);
            return true;
        } else {
            return false;
        }
    }
    
    //現在お気に入り済みかを確認する
    public function is_favorite($favoriteId)
    {
        return $this->favorites()->where('micropost_id', $favoriteId)->exists();
    }
}

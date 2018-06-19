<?php

namespace App;

use App\Transformers\UserTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use \Laravel\Passport\HasApiTokens;


    const USUARIO_VERIFICADO="1";
    const USUARIO_NO_VERIFICADO="0";

    const USUARIO_ADMINISTRADOR="true";
    const USUARIO_REGULAR="false";

    public $transformer=UserTransformer::class;

    protected $table="users";
    protected $dates=['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verified',
        'verification_token',
        'admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token'
    ];
    public function setNameAttribute($value){
        $this->attributes['name']=strtolower($value);
    }
    public function getNameAttribute($value){
        return ucwords($value);
    }
    public function setEmailAttribute($value){
        $this->attributes['email']=strtolower($value);
    }

    public function esVerificado(){
        return $this->verified==User::USUARIO_VERIFICADO;
    }

    public function esAdministrador(){
        return $this->admin==User::USUARIO_ADMINISTRADOR;
    }
    static function  generaVerificationToken(){
        return str_random(40);
    }
}
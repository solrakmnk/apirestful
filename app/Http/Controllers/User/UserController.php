<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users=User::all();

        return $this->showAll($users);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules=[
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6|confirmed'
        ];

        $this->validate($request,$rules);

        $campos=$request->all();
        $campos['password']=bcrypt($request->password);
        $campos['verified']=User::USUARIO_NO_VERIFICADO;
        $campos['verification_token']=User::generaVerificationToken();
        $campos['admin']=User::USUARIO_REGULAR;

        $usuario=User::create($campos);

        return $this->showOne($usuario,200);
    }


    public function show(User $user) //Inyeccion implicita de modelo
    {
//        $user=User::findOrFail($id);

        return response()->json(['data'=>$user],200);
    }


    public function update(Request $request, User $user)
    {

        $rules=[
        'email'=>'email|unique:users,email,'.$user->id,
        'password'=>'min:6|confirmed',
        'admin'=>'in:'.User::USUARIO_ADMINISTRADOR.",".User::USUARIO_REGULAR,
    ];
        $this->validate($request,$rules);

        if($request->has('name')){
            $user->name=$request->name;
        }
        if($request->has('email') && $user->email!=$request->email){
            $user->verified=User::USUARIO_NO_VERIFICADO;
            $user->verification_token=User::generaVerificationToken();
            $user->email=$request->email;
        }

        if($request->has('password')){
            $user->password=bcrypt($request->getPassword());
        }

        if($request->has('admin')){
            if(!$user->esVerificado()){
                return $this->errorResponse('Unicamente usuarios verificados pueden cambiar su valor como admin',409);

            }
            $user->admin=$request->admin;
        }

        if(!$user->isDirty()){
            return $this->errorResponse('Se debe especificar al menos un valor diferente',422);
        }

        $user->save();
            return $this->showOne($user,200);

    }


    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['data'=>$user],200);
    }

    public function verify($token){
        $user=User::where('verification_token',$token)->firstOrFail();
        $user->verified=User::USUARIO_VERIFICADO;
        $user->verification_token=null;
        $user->save();
        return $this->showMessage('La cuenta ha sido verificada',200);
    }
}

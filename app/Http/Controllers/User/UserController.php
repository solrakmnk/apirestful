<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users=User::all();

        return response()->json(['data'=>$users],200);
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

        return response()->json(['data'=>$usuario],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=User::findOrFail($id);

        return response()->json(['data'=>$user],200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user=User::findOrFail($id);

        $rules=[
        'email'=>'email|unique:users,email,'.$id,
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
                return response()->json(['error'=>'Unicamente usuarios verificados pueden cambiar su valor como admin',
                                         'code'=>409],409);
            }
            $user->admin=$request->admin;
        }

        if(!$user->isDirty()){
            return response()->json(['error'=>'Se debe especificar al menos un valor diferente', 'code'=>422],422);
        }

        $user->save();

        return response()->json(['data'=>$user],200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user=User::findOrFail($id);
        $user->delete();

        return response()->json(['data'=>$user],200);
    }
}

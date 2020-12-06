<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\User;
use Illuminate\Support\Str;
use  Illuminate\Support\Facades\ Storage;

class UsersController extends Controller
{
    //Prueba de emails y conexion
    public function Prueba(Request $request)
    {
        $request->validate([
            'edad' => 'required'
            ]);
        if($request->edad >= 18)
        {
            $datos = array(
                'name'=> "Jair Alejandro",
                'email'=> "jairalejandro32@outlook.com"
            );
            
            Mail::send('prueba', $datos, function($message) use ($datos) {
                $message->from('19170162@uttcampus.edu.mx', 'JAIR ALEJANDRO MARTINEZ CARRILLO');
                $message->to("jairalejandro32@outlook.com", "Jair Alejandro")->subject('Alerta');
            });
            return response()->json(['ya tas grande', $request->edad], 201);
        }
        else if($request->edad <= 17)
        {
            return response()->json(['tas muy chiquito', $request->edad], 201);
        }
    }
    //Registro de Usuarios
    public function Registro(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'age' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            //'image' => 'required'
            ]);
            /**if($request->hasFile('image'))
            {
            $path = Storage::disk('local')->putFile('perfil/', $request->image);
            }*/
            $user = new User();
            $user->name = $request->name;
            $user->age = $request->age;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->image = " ";
            $user->confirmed = false;
            $user->confirmation_code = Str::random(20);

        if ($user->save())
        {
            $datos = array(
                'email'=> $user->email,
                'name'=> $user->name,
                'confirmation_code'=> $user->confirmation_code
            );
            Mail::send('verificar', $datos, function($message) use ($datos) {
                $message->from('19170162@uttcampus.edu.mx', 'JAIR ALEJANDRO MARTINEZ CARRILLO');
                $message->to($datos['email'], $datos['name'])->subject('Por favor confirma tu correo');
            });
            //Puedes mostrar el mensaje en un toas y si funciona bien solo deja el 
            //mensaje para que no mostrar los datos que se guardaron a parte creo 
            //que te regresa un arreglo en ves de un objeto 
        return response()->json(["Usuario registrado correctamente", $user], 201);
        }
        return  response()->json("Error al registrar usuario", 400);
    }
    //Verificar cuenta 
    public function Verificar($code)
    {
        $user = User::where('confirmation_code', $code)->first();
        if($user)
        {
        $user->confirmed = true;
        $user->confirmation_code = null;
        $user->save();
        }
    }
    
    /**public function Show(Request $request)
    {
        $ruta = "mostrar usuarios";
        $role = "admin";
        if ($request->user()->tokenCan('admin'))
        {
            $mostrar = DB::select('SELECT users.name, users.email, users.age 
            FROM users
            WHERE users.role !=?',[$role]);
            return response()->json($mostrar, 201);
        }
        $datos = array(
            'name'=> "Jair Alejandro",
            'basura'=> $request->user()->name,
            'basura2'=> $request->user()->email,
            'ruta'=> $ruta
        );
        Mail::send('alert_message', $datos, function($message) use ($datos) {
            $message->from('19170162@uttcampus.edu.mx', 'JAIR ALEJANDRO MARTINEZ CARRILLO');
            $message->to("jairalejandro32@outlook.com", $datos['name'])->subject('Alerta');
        });
        return response()->json('Usted no esta autorizado', 401);
    }*/

    /**public function Update(Request $request)
    {
        $ruta = "actualizar usuario";
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'age' => 'required',
            'password' => 'required'
            ]);
        if ($request->user()->tokenCan('admin'))
        {
        $usuario_im = user::find($usuario);
        $usuario_im->update($request->all());
        $mostrar = DB::select('SELECT users.name, users.email, users.age 
        FROM users
        WHERE users.id =?',[$id]);
        return response()->json(['Usuario actualizado correctamente', $mostrar], 201);
        }
        $datos = array(
            'name'=> "Jair Alejandro",
            'basura'=> $request->user()->name,
            'basura2'=> $request->user()->email,
            'ruta'=> $ruta
        );
        Mail::send('alert_message', $datos, function($message) use ($datos) {
            $message->from('19170162@uttcampus.edu.mx', 'JAIR ALEJANDRO MARTINEZ CARRILLO');
            $message->to("jairalejandro32@outlook.com", $datos['name'])->subject('Alerta');
        });
        return response()->json('Usted no tiene los permisos para realizar esta accion', 401);
    }*/

    /**public function Delete(Request $request)
    {
        $ruta = "eliminar usuarios";
        $request->validate([
            'email' => 'required'
            ]);
            if ($request->user()->tokenCan('admin'))
            { 
                $ruta = DB::table('users')
                ->select('users.image')
                ->where('users.email', '=', $request->email)->get();
                Storage::delete($ruta);
                $usuario = DB::table('users')
                ->where('users.email', '=', $request->email)->delete();
                $id = DB::table('users')
                ->select('users.id')
                ->where('users.email', '=', $request->email)->get();
                $comentarios = DB::table('comments')
                ->where('comments.user_id', '=', $id)->delete();
                $tokens = DB::table('personal_access_tokens')
                ->where('personal_access_tokens.name', '=', $request->email)->delete();
                if ($usuario)
                {
                    return response()->json('Usuario eliminado correctamente', 201);
                }
                return response()->json('Datos erroneos', 400);
            }
            $datos = array(
                'name'=> "Jair Alejandro",
                'basura'=> $request->user()->name,
                'basura2'=> $request->user()->email,
                'ruta'=> $ruta
            );
            Mail::send('alert_message', $datos, function($message) use ($datos) {
                $message->from('19170162@uttcampus.edu.mx', 'JAIR ALEJANDRO MARTINEZ CARRILLO');
                $message->to("jairalejandro32@outlook.com", $datos['name'])->subject('Alerta');
            });    
            return response()->json('Usted no tiene los permisos para realizar esta accion', 401);
    }*/
    //LogIn Usuario
    public function LogIn(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
            ]);
            $user = User::where('email', $request->email)->first();
            $user2 = User::where([
                ['confirmed', '=', '0'],
                ['email', $request->email],
            ])->first();
            if (! $user || ! Hash::check($request->password, $user->password)) 
            {
                return response()->json('Datos erroneos', 400);
            }
            elseif($user2)
            {
                return response()->json('Para acceder a su cuenta es necesario verificarla antes, revise su correo', 400);   
            }
                $token = $user->createToken($request->email, [$user->role])->plainTextToken;
                return response()->json(["token" => $token], 201);
    }

    /**public function UpdateRole(Request $request)
    {
        $ruta = "actualizar rol";
        $request->validate([
            'id' => 'required',
            'role' => 'required'
            ]);
            if ($request->user()->tokenCan('admin'))
            { 
                DB::table('users') ->where('id', $request->id)
                ->update(['role' => $request->role]);
                DB::table('personal_access_tokens') ->where('tokenable_id', $request->id)
                ->update(['abilities' => $request->role]);
                $mostrar = DB::Select('SELECT users.name, users.email, users.role 
                FROM users
                WHERE users.id ='.$request->id);
                if ($mostrar){
                    return response()->json(['Rol de usuario actualizado, para que los permisos se actualizen elimine el token', $mostrar], 201);
                }
                return response()->json('Datos erroneos', 400);
            }
            $datos = array(
                'name'=> "Jair Alejandro",
                'basura'=> $request->user()->name,
                'basura2'=> $request->user()->email,
                'ruta'=> $ruta
            );
            Mail::send('alert_message', $datos, function($message) use ($datos) {
                $message->from('19170162@uttcampus.edu.mx', 'JAIR ALEJANDRO MARTINEZ CARRILLO');
                $message->to("jairalejandro32@outlook.com", $datos['name'])->subject('Alerta');
            });
            return response()->json('Usted no tiene los permisos para realizar esta accion', 401);
    }*/

    public function LogOut(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
            ]);
            $user = DB::table('personal_access_tokens')->where('personal_access_tokens.name', '=', $request->email);
            if($user) 
            {
                $tokens = DB::table('personal_access_tokens')
                ->where('personal_access_tokens.name', '=', $request->email)->delete();
                return response()->json('LogOut hehco de manera correcta', 200);
            }
            return response()->json('Datos erroneos', 400);
    }
    
}
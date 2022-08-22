<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class UsersController extends Controller
{
    /**
     * Display all users
     * 
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show form for creating user
     * 
     * @return \Illuminate\Http\Response
     */
    public function create() 
    {
        $roles = Role::all();
        return view('users.create',compact('roles'));
    }

    /**
     * Store a newly created user
     * 
     * @param User $user
     * @param StoreUserRequest $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) 
    {
        //For demo purposes only. When creating user or inviting a user
        // you should create a generated random password and email it to the user
        $request->merge([
            'password'=>Hash::make($request->pass)
        ]);
        $user=User::create($request->all());
        $user->assignRole($request->roles_name);
        return redirect()->route('users.index')
            ->withSuccess(__('User created successfully.'));
    }

    /**
     * Show user data
     * 
     * @param User $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function show(User $user) 
    {
        return view('users.show', [
            'user' => $user
        ]);
    }

    /**
     * Edit user data
     * 
     * @param User $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user) 
    {
        return view('users.edit', [
            'user' => $user,
            'userRole' => $user->roles->pluck('name','name')->toArray(),
            'roles' => Role::latest()->get()
        ]);
    }

    /**
     * Update user data
     * 
     * @param User $user
     * @param UpdateUserRequest $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, Request $request) 
    {

        $user->update($request->all());
        $user->syncRoles($request->get('roles_name'));

        return redirect()->route('users.index')
            ->withSuccess(__('User updated successfully.'));
    }

    /**
     * Delete user data
     * 
     * @param User $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) 
    {
        $user->delete();

        return redirect()->route('users.index')
            ->withSuccess(__('User deleted successfully.'));
    }

    public function showReport(){
        $sections=Section::all();
        return view('reports.customers',compact('sections'));
    }
    public function search(Request $request){

        $sections=Section::all();
            if($request->section_id && $request->product_id && $request->start_at=="" && $request->end_at==""){
                $invoices=Invoice::with(['details','product','section'])->where('section_id',$request->section_id)->where('product_id',$request->product_id)->get();
                return view('reports.customers',compact('invoices','sections'));
            }else{
                 $start_at = date($request->start_at);
                $end_at = date($request->end_at);
                $invoices=Invoice::with(['details','product','section'])->whereBetween('invoice_date',[$start_at,$end_at])->where('section_id',$request->section_id)->where('product_id',$request->product_id)->get();
                return view('reports.customers',compact('start_at','end_at','invoices','sections'));
            }
        }   
}
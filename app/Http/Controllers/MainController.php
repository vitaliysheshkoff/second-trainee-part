<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserPostRequest;
use App\Http\Requests\UpdateUserPutRequest;
use App\Models\MyUser;

class MainController extends Controller
{
    public function home()
    {
        $users = new MyUser();

        return view('home', ['users' => $users->all()]);
    }

    public function add(AddUserPostRequest $request)
    {
        $validated = $request->validated();

        $user = new MyUser();
        $this->setUser($request, $user);
        $user->save();

        return redirect()->route('home')->with('status', 'New user has been added successfully');
    }

    public function put(UpdateUserPutRequest $request)
    {
        $validated = $request->validated();

        $user = MyUser::query()->find($request->get('db-id'));
        $this->setUser($request, $user);
        $user->save();

        return redirect()->route('home')->with('status', 'User has been updated successfully');
    }

    public function remove($id)
    {
        $user = MyUser::query()->find($id);
        $user->delete();

        return redirect()->route('home')->with('status', 'User has been removed successfully');
    }

    public function removeAll()
    {
        MyUser::query()->truncate();

        return redirect()->route('home')->with('status', 'All users have been removed successfully');
    }

    private function setUser($request, &$user): void
    {
        $user->email = $request->input('email');
        $user->name = $request->input('name');
        $user->gender = $request->input('gender');
        $user->status = $request->input('status');
    }
}

<?php

namespace App\Http\Controllers\Api;


use DB;
use DataTables;
use App\Models\User;
use Illuminate\Http\Request;
use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;


class UserController extends Controller
{
    public function index()
    {
        $model = User::query()->with('role');

        return DataTables::of($model)
        ->editColumn('profile_image', function ($row) {
            return asset('storage/' . $row->profile_image);
        })
        ->editColumn('role', function ($row) {
            return $row->role ? $row->role->name : '';
        })
        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-M-Y');
        })
        ->filterColumn('role', function ($query, $keyword) {
            $query->whereHas('role', function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            });
        })
        ->orderColumn('role', function($query, $order) {
            $query->join('roles', 'roles.id', '=', 'users.role_id')
                  ->orderBy('roles.name', $order);
        })
        ->toJson();

    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => ['required','email','regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i','unique:users,email,NULL,id,deleted_at,NULL'],
            'phone'          => ['required','numeric','regex:/^[0-9]{10}$/'],
            'description'    => 'required|string',
            'role_id'        => 'required|exists:roles,id',
            'profile_image'  => ['required', 'file','image', 'max:2048', 'mimes:jpeg,png,jpg'],

        ],[
            'profile_image.image' => 'Please upload image.',
            'profile_image.mimes' => 'Please upload image with extentions: jpeg,png,jpg.',
            'profile_image.max'   => 'The image size must equal or less than 2MB',
        ],[
            'role_id' => 'role'
        ]);


        $profileImage = null;
        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image')->store('profile_images', 'public');
        }

        try {

            DB::beginTransaction();

            $data = [
                'name'          => ucwords($request->name),
                'email'         => $request->email,
                'phone'         => $request->phone,
                'description'   => $request->description,
                'role_id'       => $request->role_id,
                'profile_image' => $profileImage,
            ];

            User::create($data);

            DB::commit();

            $responseData = [
                'status'  => true,
                'message' => 'User Created Successfully!'
            ];
            return response()->json($responseData,200);

        } catch (\Exception $e) {
            DB::rollBack();

            // dd($e->getMessage().' '.$e->getFile().' '.$e->getCode());

            return response()->json(['status'=>false, 'message'=>'Something went wrong!'],500);
        }
    }

}

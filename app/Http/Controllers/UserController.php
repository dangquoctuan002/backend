<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $users = $this->userRepository->listWhere(
            [
                "page" => $request->get('page', 1),
                "limit" => 9
            ]
        );
        return response()->json($users);
    }


    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['status' => 1, 'data' => $user]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|unique:users,phone',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'gender' => 'nullable|in:male,female,other',
            'birthday' => 'nullable|date|before:-12 years',
            'account' => 'required|alpha_dash|unique:users,account',
        ]);

        $hashedPassword = bcrypt($request->password);

        $validated['password'] = $hashedPassword;
        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        // Tìm user theo id
        $user = User::findOrFail($id);

        // Định nghĩa rules để validate dữ liệu
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => [
                'nullable',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                Rule::unique('users')->ignore($user->id),
            ],
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female,other',
            'birthday' => 'nullable|date|before:today - 12 years',
            'account' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ];

        // Validate dữ liệu gửi lên
        $validator = Validator::make($request->all(), $rules);

        // Nếu có lỗi validate, trả về lỗi
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cập nhật dữ liệu cho các trường có dữ liệu gửi lên
        if ($request->has('name')) {
            $user->name = $request->input('name');
        }
        if ($request->has('phone')) {
            $user->phone = $request->input('phone');
        }
        if ($request->has('address')) {
            $user->address = $request->input('address');
        }
        if ($request->has('gender')) {
            $user->gender = $request->input('gender');
        }
        if ($request->has('birthday')) {
            $user->birthday = $request->input('birthday');
        }
        if ($request->has('account')) {
            $user->account = $request->input('account');
        }
        if ($request->has('email')) {
            $user->email = $request->input('email');
        }

        // Lưu thay đổi
        $user->save();

        // Trả về response thành công
        return response()->json(['message' => 'User updated successfully'], 200);
    }
   
    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['errors' => ['current_password' => ['Mật khẩu hiện tại không đúng.']]], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Đổi mật khẩu thành công'], 200);
    }


    public function updateimage(Request $request, $id)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('images', $imageName, 'public');

            // Update user's image path in database
            $user = User::findOrFail($id);
            $user->image = '/storage/' . $imagePath; // assuming 'public' disk is configured

            $user->save();

            return response()->json(['message' => 'Hình ảnh đã được cập nhật thành công'], 200);
        }

        return response()->json(['message' => 'Không tìm thấy hình ảnh để cập nhật'], 404);
    }



}

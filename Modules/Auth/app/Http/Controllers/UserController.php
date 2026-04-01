<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Auth\Models\User;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function getAllUsers(Request $request)
    {
        $query = User::query();

        if ($request->has('name') && $request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('email') && $request->email) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->has('phone') && $request->phone) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        if ($request->has('marital_status') && $request->marital_status) {
            $query->where('marital_status', $request->marital_status);
        }

        if ($request->has('life_style_id') && $request->life_style_id) {
            $query->where('life_style_id', $request->life_style_id);
        }

        $users = $query->paginate();

        return $this->respondOk($users, 'Users retrieved successfully');
    }

    public function showProfile()
    {
        $user = User::with('children', 'lifeStyle','lifeElements')->find(Auth::id());
        return $this->respondOk($user, 'User Profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string',
            'email' => ['nullable', 'email', 'unique :users:email', Rule::unique('users')->ignore(Auth::id())],
            'country_code' => 'nullable|string',
            'phone' => ['nullable', 'string', 'unique:users,phone', Rule::unique('users')->ignore(Auth::id())],
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'gender' => ['nullable', Rule::in(['male','female'])],
            'marital_status' => ['nullable', Rule::in(['single','married','divorced','widowed'])],
            'life_style_id' => 'nullable|exists:life_styles,id',
            'life_element_ids' => 'nullable|array',
            'life_element_ids.*' => 'exists:life_elements,id',
        ]);

        $user = User::find(Auth::id());

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->profile_image));
            }

            $file = $request->file('profile_image');
            $fileName = 'profile_image_' . time() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('Profile_Images', $fileName, 'public');
            $user->profile_image = Storage::url($path);
        }

        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'phone' => $request->phone ?? $user->phone,
            'country_code' => $request->country_code ?? $user->country_code,
            'gender' => $request->gender ?? $user->gender,
            'marital_status' => $request->marital_status ?? $user->marital_status,
            'life_style_id' => $request->life_style_id ?? $user->life_style_id,
        ]);

        if($request->has('life_element_ids') && is_array($request->input('life_element_ids'))) {
            $user->lifeElements()->sync($request->input('life_element_ids'));
        }

        return $this->respondOk($user, 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->respondOk(null, "user password updated successfully.");
    }

    public function deleteUser()
    {
       $user = User::findOrFail(Auth::id());
 
       if($user->profile_image){
            Storage::disk('public')->delete(str_replace('/storage/', '', $user->profile_image));
        }
        if($user->children)
        {
            $user->children()->delete();
        }
       $user->delete();
       return $this->respondOk(null, 'User deleted successfully');
    }
}

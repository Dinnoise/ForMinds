<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Post;
use App\Models\Subscription;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * мой профиль
     */
    public function myProfile()
    {
        $user = Auth::user();

        $posts = $user->posts()->published()
            ->withCount(['likes', 'comments'])
            ->latest()
            ->get();

        return view('profile.me', compact('user', 'posts'));
    }

    public function myDrafts()
    {
        $user = Auth::user();

        $drafts = $user->posts()->drafts()
            ->with('tags')
            ->latest('updated_at')
            ->get();

        return view('profile.drafts', compact('user', 'drafts'));
    }

    public function toggleSubscription(User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $user->id) {
            return redirect()->back();
        }

        $existing = $currentUser->subscriptions()
            ->where('following_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            $currentUser->subscriptions()->create([
                'following_id' => $user->id,
            ]);
        }

        return redirect()->back();
    }

    public function myComments()
    {
        $user = Auth::user();

        $comments = $user->comments()
            ->with('post.user')
            ->latest()
            ->get();

        return view('profile.comments', compact('user', 'comments'));
    }

    public function myLikes()
    {
        $user = Auth::user();

        $likes = $user->likes()
            ->with('post.user')
            ->latest()
            ->get();

        return view('profile.likes', compact('user', 'likes'));
    }

    public function mySubscriptions()
    {
        $user = Auth::user();

        $subscriptions = $user->following()->get();

        return view('profile.subscriptions', compact('user', 'subscriptions'));
    }

    /** Профиль другого пользователя */
    public function show(User $user)
    {
        $posts = $user->posts()->published()
            ->withCount(['likes', 'comments'])
            ->latest()
            ->get();

        return view('profile.show', compact('user', 'posts'));
    }

    public function posts(User $user)
    {
        $posts = $user->posts()->published()
            ->withCount(['allComments', 'likes'])
            ->latest()
            ->get();

        return view('profile.posts', compact('user', 'posts'));
    }

    public function comments(User $user)
    {
        $comments = $user->comments()
            ->with('post.user')
            ->latest()
            ->get();

        return view('profile.comments', compact('user', 'comments'));
    }

    public function likes(User $user)
    {
        $likes = $user->likes()
            ->with('post.user')
            ->latest()
            ->get();

        return view('profile.likes', compact('user', 'likes'));
    }
}

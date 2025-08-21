<?php

namespace App\View\Composers;

use App\Models\Profile;
use Illuminate\View\View;

class ProfileComposer
{
    public function compose(View $view)
    {
        // Get the first profile record or create an empty model
        $profile = Profile::first() ?: new Profile();
        
        // Share the profile with the view
        $view->with('profile', $profile);
    }
}
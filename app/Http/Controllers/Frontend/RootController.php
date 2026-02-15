<?php

namespace App\Http\Controllers\Frontend;


 
use App\Enums\Status;
use App\Enums\Activity;
use Smartisan\Settings\Facades\Settings;
 
use Illuminate\Support\Facades\Auth;

 

use App\Models\Analytic;
use App\Models\ThemeSetting;
 
use App\Http\Controllers\Controller;
use App\Enums\Role as EnumRole;
use App\Models\User;
 

class RootController extends Controller
{

    public function index(): \Illuminate\Contracts\View\Factory | \Illuminate\Contracts\View\View | \Illuminate\Contracts\Foundation\Application
  {
        $analytics =  Analytic::with('analyticSections')->where(['status' => Status::ACTIVE])->get();
        $themeFavicon = ThemeSetting::where(['key' => 'theme_favicon_logo'])->first();
        $maint = Settings::group('site')->get('maintenance') ;
        $maintenance_message = Settings::group('site')->get('maintenance_message');

   
                 $favIcon = $themeFavicon->faviconLogo;
         return view('master', ['analytics' => $analytics, 'favicon' => $favIcon, 'maintenance' => $maint, 'maintenance_message' => $maintenance_message ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
  public function changeLanguage($locale)
{
    // TEMPORARY DEBUGGING LINE: Confirm this method is even being hit.
    dd('LanguageController@changeLanguage hit! Attempting to set locale to: ' . $locale);

    $availableLocales = ['en', 'ar'];
    if (! in_array($locale, $availableLocales)) {
        $locale = config('app.fallback_locale');
    }

    App::setLocale($locale);
    Session::put('locale', $locale);

    // TEMPORARY DEBUGGING LINE: Check session *after* setting
    dd('Locale set in session. Session locale: ' . Session::get('locale') . ', App locale: ' . App::getLocale());

    return redirect()->back();
}
}

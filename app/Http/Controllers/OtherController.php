<?php

namespace App\Http\Controllers;

use App\Image;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;

class OtherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function error404(Request $request)
    {
        if (Auth::check())
        {
            if (!empty(Auth::user()->selected_locale))
            {
                $locale = Auth::user()->selected_locale;
            }
        }

        if (empty($locale))
        {
            if ($request->hasCookie('locale')) {
                if (in_array($request->cookie('locale'), Config::get('app.locales'))) {
                    $locale = $request->cookie('locale');
                }
            }
        }

        if (empty($locale)) {
            foreach ($request->getLanguages() as $value) {
                if (mb_strlen($value) == 2) {
                    if (empty($locale)) {
                        if (in_array($value, config('app.locales'))) {
                            $locale = $value;

                            break;
                        }
                    }
                }
            }
        }

        if (empty($locale))
            $locale = Config::get('app.locale');

        $url = \Litlife\Url\Url::fromString($request->fullUrl());

        if ($url->getSegment(1) != $locale)
        {
            $url = $url->withPath('/'.$locale.$url->getPath());

            return redirect()->to($url);
        }

        URL::defaults(['locale' => $locale]);

        return response()
            ->view('errors.404', [], 404);
    }

    public function previewNotification()
    {
        $environment = App::environment();

        if (App::environment(['local', 'testing'])) {
            $message = (new TestNotification())->toMail('test@email.com');

            $markdown = new Markdown(view(), config('mail.markdown'));

            return $markdown->render('vendor.notifications.email', $message->toArray());
        } else {
            return abort(404);
        }
    }

    public function test(Browsershot $browsershot)
    {
        $content = $browsershot
            ->url('http://www.sdfsrwesvdfsdfsdf.com')
            ->timeout(60)
            ->windowSize(1000, 1000)
            ->setScreenshotType('jpeg', 100)
            ->setDelay(5000)
            ->dismissDialogs()
            ->ignoreHttpsErrors()
            ->screenshot();
    }

    public function phpinfo()
    {
        phpinfo();
    }

    public function sitemapRedirect()
    {
        if (Storage::disk('public')->exists('sitemap/sitemap.xml'))
            return redirect()
                ->away(Storage::disk('public')
                    ->url('sitemap/sitemap.xml'));
        else
            abort(404);

    }

    public function personalDataProcessingAgreement()
    {
        return view('other.personal_data_processing_agreement');
    }

    public function privacyPolcy()
    {
        return view('other.privacy_policy');
    }
}

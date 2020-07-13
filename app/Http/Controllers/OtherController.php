<?php

namespace App\Http\Controllers;

use App\Image;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class OtherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function error404()
    {
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

    public function test()
    {
        \Notification::route('mail', 'sites.reviews.com@gmail.com')
            ->notify(new TestNotification());

        return view('test');

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
        return view('personal_data_processing_agreement');
    }
}

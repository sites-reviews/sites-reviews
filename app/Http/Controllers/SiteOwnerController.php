<?php

namespace App\Http\Controllers;

use App\ProofOwnership;
use App\Service\DNS;
use App\Site;
use App\SiteOwner;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class SiteOwnerController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param \App\Site $site
     * @return \Illuminate\Http\Response
     */
    public function request(Site $site)
    {
        $userOwner = $site->userOwner;

        if (empty($userOwner)) {
            $siteOwner = $site->siteOwners()
                ->where('create_user_id', Auth::id())
                ->first();

            if (empty($siteOwner)) {
                $siteOwner = new SiteOwner();
                $siteOwner->create_user_id = Auth::id();
                $siteOwner->site_id = $site->id;
                $siteOwner->statusSentForReview();
                $siteOwner->save();

                $proof = new ProofOwnership();
                $proof->siteOwner()->associate($siteOwner);
                $proof->save();
            } else {
                $proof = $siteOwner->proof()
                    ->first();
            }
        }

        return view('site.verification.request', [
            'site' => $site,
            'proof' => $proof ?? null,
            'userOwner' => $userOwner
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Site $site
     * @return \Illuminate\Http\Response
     */
    public function checkDns(Site $site, DNS $dns)
    {
        try {
            $collection = $dns->getRecord($site->domain, DNS_TXT);
        } catch (\ErrorException $exception) {

            $message = $exception->getMessage();

            $message = trim(Str::after($message, 'dns_get_record():'));

            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors(['error' => __($message)], 'check_dns');
        }

        if ($collection->isEmpty() or $collection->where('txt')->isEmpty()) {
            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors(['error' => __('No TXT records found')], 'check_dns');
        }

        $verificationRecordsCollection = $collection->filter(function ($record, $key) {

            $txt = trim($record['txt']);

            if (preg_match('/^' . preg_quote(config('verification.dns_key_name')) . '(?:[[:space:]]*)\:(.*)/iu', $txt, $matches))
                return true;

        })->transform(function ($record, $key) {

            $txt = trim($record['txt']);

            if (preg_match('/^' . preg_quote(config('verification.dns_key_name')) . '(?:[[:space:]]*)\:(.*)/iu', $txt, $matches)) {
                return trim($matches[1]);
            }
        });

        if ($verificationRecordsCollection->isEmpty()) {
            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors(['error' => __('No :dns_key_name TXT record found', ['dns_key_name' => config('verification.dns_key_name')])], 'check_dns');
        }

        $siteOwner = $site->siteOwners()
            ->where('create_user_id', Auth::id())
            ->whereHas('proof', function (Builder $query) use ($verificationRecordsCollection) {
                $query->whereDnsCodes('dns_code', $verificationRecordsCollection);
            })
            ->first();

        if (empty($siteOwner)) {
            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors(['error' => __('TXT :dns_key_name does not match the desired value', ['dns_key_name' => config('verification.dns_key_name')])], 'check_dns');
        } else {
            $siteOwner->statusAccepted();
            $siteOwner->save();

            $site->userOwner()->associate($siteOwner->create_user);
            $site->save();

            return redirect()
                ->route('sites.verification.request', $site)
                ->with(['success' => __("TXT record found.")]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Site $site
     * @return \Illuminate\Http\Response
     */
    public function checkFile(Site $site, Client $client)
    {
        $siteOwner = $site->siteOwners()
            ->where('create_user_id', Auth::id())
            ->first();

        $proof = $siteOwner->proof()->first();

        try {
            $options = config('guzzle.request.options');

            $options['allow_redirects'] = [
                'max' => 5,
                'protocols' => ['http', 'https'],
                'on_redirect' => function (
                    RequestInterface $request,
                    ResponseInterface $response,
                    UriInterface $uri
                ) {
                    if ($request->getUri()->getHost() != $uri->getHost()) {
                        throw new \Exception('Redirect to another host');
                    }
                }
            ];

            $response = $client->request('GET', $proof->getFileUrl(), $options);

        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() == 404) {
                return redirect()
                    ->route('sites.verification.request', $site)
                    ->withErrors(['error' => __('The file with the required name was not found')], 'check_file');
            } else {
                return redirect()
                    ->route('sites.verification.request', $site)
                    ->withErrors(['error' => __('File access error :status_code :reason_phrase', [
                        'status_code' => $exception->getResponse()->getStatusCode(),
                        'reason_phrase' => $exception->getResponse()->getReasonPhrase()
                    ])], 'check_file');
            }
        } catch (ConnectException $exception) {

            $context = $exception->getHandlerContext();

            if ($context['errno'] == 6) {
                return redirect()
                    ->route('sites.verification.request', $site)
                    ->withErrors(['error' => __('Could not resolve host :domain', [
                        'domain' => $site->domain
                    ])], 'check_file');
            }

            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors(['error' => __('Site connection error :errno :error_text', [
                    'errno' => $context['errno'],
                    'error_text' => $context['error']
                ])], 'check_file');

        } catch (\Exception $exception) {

            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors([
                    'error' => __('Error checking verification using a file')
                ], 'check_file');
        }

        if ($response->getStatusCode() == 302)
            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors(['error' => __('A redirect occurred instead of the file')], 'check_file');

        if ($response->getStatusCode() != 200)
            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors(['error' => __('Response code is :code (the code must be 200)', ['code' => $response->getStatusCode()])], 'check_file');

        $contents = $response->getBody()
            ->getContents();

        if ($proof->file_code == trim($contents)) {
            $siteOwner->statusAccepted();
            $siteOwner->save();

            $site->userOwner()->associate($siteOwner->create_user);
            $site->save();

            return redirect()
                ->route('sites.verification.request', $site)
                ->with(['success' => __('The file with the required content is found on the site.')]);
        } else
            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors(['error' => __('The file content does not match the desired content')], 'check_file');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Site $site
     * @return \Illuminate\Http\Response
     */
    public function downloadFile(Site $site, Client $client)
    {
        $siteOwner = $site->siteOwners()
            ->where('create_user_id', Auth::id())
            ->first();

        $proof = $siteOwner->proof()->first();

        return response($proof->file_code, 200)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $proof->file_path . '"');
    }
}

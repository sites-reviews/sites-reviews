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

        return view('site.verification.request', [
            'site' => $site,
            'proof' => $proof
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
        $collection = $dns->getRecord($site->domain, DNS_TXT);

        if ($collection->isEmpty() or $collection->where('txt')->isEmpty()) {
            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors(['error' => __('verification.no_txt_records_were_found')], 'check_dns');
        }

        $verificationRecordsCollection = $collection->filter(function ($record, $key) {

            $txt = trim($record['txt']);

            if (preg_match('/^'.preg_quote(config('verification.dns_key_name')).'(?:[[:space:]]*)\:(.*)/iu', $txt, $matches))
                return true;

        })->transform(function ($record, $key) {

            $txt = trim($record['txt']);

            if (preg_match('/^'.preg_quote(config('verification.dns_key_name')).'(?:[[:space:]]*)\:(.*)/iu', $txt, $matches))
            {
                return trim($matches[1]);
            }
        });

        if ($verificationRecordsCollection->isEmpty()) {
            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors(['error' => __('verification.required_txt_record_was_not_found')], 'check_dns');
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
                ->withErrors(['error' => __('verification.txt_does_not_match_the_desired_value')], 'check_dns');
        } else {
            $siteOwner->statusAccepted();
            $siteOwner->save();

            return redirect()
                ->route('sites.verification.request', $site)
                ->with(['success' => __('verification.record_found_and_rights_verified')]);
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
            $response = $client->request(
                'GET',
                $proof->getFileUrl(),
                [
                    'allow_redirects' => false,
                    'connect_timeout' => 5,
                    'read_timeout' => 5,
                    'timeout' => 5
                ]
            );
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() == 404)
            {
                return redirect()
                    ->route('sites.verification.request', $site)
                    ->withErrors(['error' => __('verification.the_file_with_the_required_name_was_not_found')], 'check_file');
            }
            else
            {
                return redirect()
                    ->route('sites.verification.request', $site)
                    ->withErrors(['error' => __('verification.error_connecting_to_the_site_server', [
                        'status_code' => $exception->getResponse()->getStatusCode(),
                        'reason_phrase' => $exception->getResponse()->getReasonPhrase()
                    ])], 'check_file');
            }
        } catch (ConnectException $exception) {

            $context = $exception->getHandlerContext();

            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors(['error' => __('verification.error_connecting_to_the_site', [
                    'errno' => $context['errno'],
                    'error_text' => $context['error']
                ])], 'check_file');

        } catch (\Exception $exception) {

            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors([
                    'error' => __('verification.error_when_checking_verification_using_a_file')
                ], 'check_file');
        }

        if ($response->getStatusCode() == 302)
            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors(['error' => __('verification.a_redirect_occurred_instead_of_the_file')], 'check_file');

        $contents = $response->getBody()
            ->getContents();

        if ($proof->file_code == trim($contents))
            return redirect()
                ->route('sites.verification.request', $site)
                ->with(['success' => __('verification.the_file_with_the_desired_content_is_found_on_the_site_verification_completed')]);
        else
            return redirect()
                ->route('sites.verification.request', $site)
                ->withErrors(['error' => __('verification.the_file_content_does_not_match_the_desired_content')], 'check_file');
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

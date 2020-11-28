<?php

namespace App\Http\Controllers;

use App\Http\Requests\TranslateValidationRequest;
use App\Services\TranslateService;
use GuzzleHttp\Exception\GuzzleException;

class MainController extends Controller
{
    private $translateService;

    public function __construct()
    {
        $this->translateService = new TranslateService('auto', 'hy');
    }

    public function index()
    {
        try {
            $result = $this->translateService->translate('');
        } catch (\ErrorException $e) {
        } catch (GuzzleException $e) {
        }
        return view('home', compact('result'));
    }

    public function translate(TranslateValidationRequest $request)
    {
        try {
            $result = $this->translateService->translate($request->text);
        } catch (\ErrorException $e) {
        } catch (GuzzleException $e) {
        }
        return view('home', compact('result'));
    }
}

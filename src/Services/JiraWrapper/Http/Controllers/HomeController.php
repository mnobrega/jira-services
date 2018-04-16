<?php
namespace App\Services\JiraWrapper\Http\Controllers;

use App\Services\JiraWrapper\Features\GetWrapperStatusFeature;
use Illuminate\Http\Request;
use Lucid\Foundation\Http\Controller;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $featureResult = $this->serve(GetWrapperStatusFeature::class);
        return view('jira_wrapper::home')->with('featureResult',$featureResult);
    }
}

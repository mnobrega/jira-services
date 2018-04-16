<?php
namespace App\Services\JiraWrapper\Http\Controllers;

use App\Services\JiraWrapper\Features\GetActivityReportFeature;
use Illuminate\Http\Request;
use Lucid\Foundation\Http\Controller;

class ActivityReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $featureResult = $this->serve(GetActivityReportFeature::class);
        return view ('jira_wrapper::activity-report');
    }
}

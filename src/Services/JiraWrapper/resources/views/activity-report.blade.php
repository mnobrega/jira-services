@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Wrapper - Activity Report</div>
                    <div class="panel-body">
                        <table class="table">
                            <thead>
                            <th>Issue Key</th>
                            <th>Summary</th>
                            <th>Time Spent (hours)</th>
                            <th>Time Spent (percentage)</th>
                            </thead>
                            <tbody>
                                @foreach($issuesTimeSpent['issues'] as $issueTimeSpent)
                                    <tr>
                                        <td>{{$issueTimeSpent['issue']->issue_key}}</td>
                                        <td>{{$issueTimeSpent['issue']->summary}}</td>
                                        <td>{{$issueTimeSpent['time_spent_in_hours']}}</td>
                                        <td>{{round(($issueTimeSpent['time_spent_in_hours']/$issuesTimeSpent['total_time_spent_in_hours'])*100,2)}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection